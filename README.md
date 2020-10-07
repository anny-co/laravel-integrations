# Laravel Integrations

This package makes it easy to get started with integrations for your laravel app.

## Installation

You can install the package with composer

```bash
composer require bddy/laravel-integrations
```

## Getting started

After you installed the package you can install the migrations.

```
php artisan migrate
```

Or, if you need to change the migration you can publish ist:

```
php artisan vendor:publish --provider=Bddy\Integrations\IntegrationsServiceProvider --tag=migrations
```

If you are using own migrations you have to publish the configuration

```
php artisan vendor:publish --provider=Bddy\Integrations\IntegrationsServiceProvider --tag=migrations
```

and set the `loadMigrations` parameter to `false`.
