<?php

namespace LaveroAdmin\Admin\ActivityLog;

use LaveroAdmin\Admin\Admin;
use LaveroAdmin\Admin\Extension;

class ActivityLog extends Extension
{
    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('activity-log', __CLASS__);
    }

    /**
     * Register routes for LaveroAdmin.
     *
     * @return void
     */
    protected static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('activity-log', 'LaveroAdmin\Admin\ActivityLog\ActivityLogController@index')->name('activity-log-index');
            $router->get('activity-log/detail', 'LaveroAdmin\Admin\ActivityLog\ActivityLogController@detail')->name('activity-log-detail');
            $router->post('activity-log/clean', 'LaveroAdmin\Admin\ActivityLog\ActivityLogController@clean')->name('activity-log-clean');
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        parent::createMenu('Activity Log', 'activity-log', 'icon-history');

        parent::createPermission('Activity Log', 'ext.activity-log', 'activity-log*');
    }
}
