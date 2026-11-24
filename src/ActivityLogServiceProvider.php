<?php

namespace LaveroAdmin\Admin\ActivityLog;

use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lavero-admin-activity-log');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lavero-admin-activity-log');

        ActivityLog::boot();
    }
}
