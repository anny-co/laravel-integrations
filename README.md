# Laravel Integrations

This package makes it easy to get started with integrations for your laravel app.

## Installation

You can install the package with composer

```bash
composer require bddy/laravel-integrations
```

### Preparing the database

You have to publish and run the migration:

```
php artisan vendor:publish --provider="Bddy\Integrations\IntegrationsServiceProvider" --tag=migrations
php artisan migrate
```

You could also publish the configuration:

```
php artisan vendor:publish --provider="Bddy\Integrations\IntegrationsServiceProvider" --tag=migrations
```
