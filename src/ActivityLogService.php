<?php

namespace LaveroAdmin\Admin\ActivityLog;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActivityLogService
{
    /**
     * Get table name.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('activitylog.table_name', 'activity_log');
    }

    /**
     * Check if table exists.
     *
     * @return bool
     */
    public function isInstalled(): bool
    {
        try {
            return Schema::hasTable($this->getTable());
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get summary statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        if (!$this->isInstalled()) {
            return [
                'total'     => 0,
                'today'     => 0,
                'created'   => 0,
                'updated'   => 0,
                'deleted'   => 0,
                'installed' => false,
            ];
        }

        $table = $this->getTable();
        $today = Carbon::today();

        return [
            'total'     => DB::table($table)->count(),
            'today'     => DB::table($table)->where('created_at', '>=', $today)->count(),
            'created'   => DB::table($table)->where('event', 'created')->count(),
            'updated'   => DB::table($table)->where('event', 'updated')->count(),
            'deleted'   => DB::table($table)->where('event', 'deleted')->count(),
            'installed' => true,
        ];
    }

    /**
     * Get activities list.
     *
     * @param array $filters
     * @param int $limit
     * @return array
     */
    public function getActivities(array $filters = [], int $limit = 50): array
    {
        if (!$this->isInstalled()) {
            return [];
        }

        $query = DB::table($this->getTable())->orderByDesc('id');

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['subject_type'])) {
            $query->where('subject_type', 'like', '%'.$filters['subject_type'].'%');
        }

        $rows = $query->limit($limit)->get();

        $list = [];
        foreach ($rows as $row) {
            $properties = json_decode($row->properties, true) ?: [];
            $hasDiff = !empty($properties['old']) || !empty($properties['attributes']);

            $subjectShort = $row->subject_type ? class_basename($row->subject_type) : '-';
            $causerShort = $row->causer_type ? class_basename($row->causer_type) : '-';

            $list[] = [
                'id'            => $row->id,
                'log_name'      => $row->log_name ?: 'default',
                'description'   => $row->description,
                'subject_type'  => $row->subject_type,
                'subject_short' => $subjectShort,
                'subject_id'    => $row->subject_id,
                'causer_type'   => $row->causer_type,
                'causer_short'  => $causerShort,
                'causer_id'     => $row->causer_id,
                'event'         => $row->event ?: 'custom',
                'has_diff'      => $hasDiff,
                'created_at'    => $row->created_at,
                'age'           => Carbon::parse($row->created_at)->diffForHumans(),
            ];
        }

        return $list;
    }

    /**
     * Get activity details with field-by-field diff.
     *
     * @param int|string $id
     * @return array|null
     */
    public function getActivityDetail($id): ?array
    {
        if (!$this->isInstalled()) {
            return null;
        }

        $row = DB::table($this->getTable())->where('id', $id)->first();
        if (!$row) {
            return null;
        }

        $properties = json_decode($row->properties, true) ?: [];
        $old = $properties['old'] ?? [];
        $attributes = $properties['attributes'] ?? [];

        // Build diff map
        $keys = array_unique(array_merge(array_keys($old), array_keys($attributes)));
        $diff = [];

        foreach ($keys as $key) {
            $oldVal = $old[$key] ?? null;
            $newVal = $attributes[$key] ?? null;

            if ($oldVal === $newVal && !empty($old)) {
                continue;
            }

            $diff[] = [
                'field' => $key,
                'old'   => is_array($oldVal) ? json_encode($oldVal, JSON_UNESCAPED_UNICODE) : (string)$oldVal,
                'new'   => is_array($newVal) ? json_encode($newVal, JSON_UNESCAPED_UNICODE) : (string)$newVal,
            ];
        }

        return [
            'id'           => $row->id,
            'description'  => $row->description,
            'subject_type' => $row->subject_type,
            'subject_id'   => $row->subject_id,
            'causer_id'    => $row->causer_id,
            'event'        => $row->event,
            'created_at'   => $row->created_at,
            'diff'         => $diff,
            'raw'          => json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
    }

    /**
     * Clean old activity log records.
     *
     * @param int $days
     * @return string
     */
    public function cleanOld(int $days = 30): string
    {
        Artisan::call('activitylog:clean', ['--days' => $days]);

        return trim(Artisan::output());
    }
}
