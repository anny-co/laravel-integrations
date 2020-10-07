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
php artisan vendor:publish --provider="Bddy\Integrations\IntegrationsServiceProvider" --tag=config
```


## Understanding how integrations work

To use integrations you need a model which is "integratable". Which means this kind of model has some integrations attached. When you have this in mind it's easy to create and manage integrations: just create, read, update and delete them like regular models. For this you need of course routes and controller. This package does not ship routes or controller, you have to create them on your own. 

Basically an integration is a model which holds settings. We recommend using an own `Integrations` folder in which every integration has it's own folder. An integration should have at least one class which implements the `Integration` contract and a service provider which will register all hooks an which the integration will act.

You can use the `HasIntegrations` trait on the model which has integrations.

## Creating integration

Coming soon:

`php artisan make:integration Slack`

This will create a folder in your `Integrations` folder with a class `Slack` and `SlackIntegrationServiceProvider`.

Register this integration

```
integrations()->registerIntegration(new Slack());
```