# Activity Log & Audit Trail for LaveroAdmin

Eloquent models audit trail and activity history extension for LaveroAdmin based on [Spatie Laravel Activitylog](https://github.com/spatie/laravel-activitylog).

## Installation

```bash
composer require laveroadmin/activity-log
```

Publish Spatie Activitylog migration:

```bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

Import menu item and permissions:

```bash
php artisan admin:import activity-log
```

## Features

- **Audit Trail Dashboard**: View created, updated, and deleted model events across your entire application.
- **Visual Diff Viewer**: Field-by-field color-coded comparison highlighting old vs. new values.
- **Filter & Search**: Filter activity records by model class, event type (`created`, `updated`, `deleted`), and date.
- **Automated Cleanup**: Purge outdated logs with `activitylog:clean`.

## License

The MIT License (MIT).
