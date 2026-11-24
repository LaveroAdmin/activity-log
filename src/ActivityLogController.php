<?php

namespace LaveroAdmin\Admin\ActivityLog;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LaveroAdmin\Admin\Facades\Admin;
use LaveroAdmin\Admin\Layout\Content;

class ActivityLogController
{
    protected ActivityLogService $service;

    public function __construct(ActivityLogService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): Content
    {
        return Admin::content(function (Content $content) use ($request) {
            $content->header(trans('lavero-admin-activity-log::activity.title'));
            $content->description(trans('lavero-admin-activity-log::activity.description'));

            $filters = [
                'event'        => $request->input('event'),
                'subject_type' => $request->input('subject_type'),
            ];

            $stats = $this->service->getStats();
            $activities = $this->service->getActivities($filters, 100);

            $content->body(view('lavero-admin-activity-log::index', compact('stats', 'activities', 'filters')));
        });
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $detail = $this->service->getActivityDetail($id);

        if (!$detail) {
            return response()->json([
                'status'  => false,
                'message' => 'Activity record not found',
            ]);
        }

        return response()->json([
            'status' => true,
            'data'   => $detail,
        ]);
    }

    public function clean(Request $request): JsonResponse
    {
        $days = (int)$request->input('days', 30);

        try {
            $output = $this->service->cleanOld($days);

            return response()->json([
                'status'  => true,
                'message' => trans('lavero-admin-activity-log::activity.clean_success'),
                'output'  => $output,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
