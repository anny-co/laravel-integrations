# Laravel Integrations

This package makes it easy to get started with integrations for your laravel app.

## Installation

You can install the package with composer

```bash
composer require bddy/laravel-integrations
```

### Preparing the database and publish config

You have to publish and run the migration:

```
php artisan vendor:publish --provider="Bddy\Integrations\IntegrationsServiceProvider" --tag=migrations
php artisan migrate
```

You could also publish the configuration:

```
php artisan vendor:publish --provider="Bddy\Integrations\IntegrationsServiceProvider" --tag=config
```

## Use own integration model

To use your own Integration you have to create it with `php artisan make:model Integration`.

This have to implement the integration contract. `class Integration extends Model implements Bddy\Integrations\Contracts\Integration`.
You can use our `IsIntegrationModel` trait.

```php
use Bddy\Integrations\Contracts\IntegrationModel as IntegrationContract;
use Bddy\Integrations\Traits\IsIntegrationModel;
use \Illuminate\Database\Eloquent\Model;
class Integration extends Model implements IntegrationContract
{
    use IsIntegrationModel;

    /**
     * @var string[]
     */
    protected $guarded = ['uuid'];

    /**
     * @var string[]
     */
    protected $casts = [
        'active' => 'boolean',
        'settings' => 'json'
    ];

    /**
     * Hide error details from user.
     * @var string[]
     */
    protected $hidden = [
        'error_details',
    ];

    /**
     * Generate uuid on creation.
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    /**
     * @return mixed|string
     */
    public function getRouteKey()
    {
        return $this->uuid;
    }
}
```

In `integrations.php` use 

```php
use App\Integration;

return [
    'integrationModel' => Integration::class,
];
```

## Understanding the concepts

To use integrations you need a model which is "integratable". This means that this kind of model has some integrations attached. When you have this in mind it's easy to create and manage integrations: just create, read, update and delete them like regular models. For this you need of course routes and controller. This package does not ship routes or controller, you have to create them on your own. 

Basically an integration is a model which holds settings. We recommend using an own `Integrations` folder in which every integration has it's own folder.

### Integration manager and integration model

The integration manager is a general integration class, which can manage a specific integration instances (model). So you have one integration manager for an integration like Slack and multiple integration instances (eloquent models) for this integration.

You do action through a manager on specific a integration model. Because an integration model can be any integration it is not practical to define for example Slack specific functions on this model. These functions go into the manager which modifies then the integration model.

### Integration service provider

The integration service provider is just a normal laravel service provider which needs to be registered. This service provider registers the integration and hooks.

You listen for specific events in your application and run then for example a job.

## Creating integration

You can use a make command to create a new integration.

`php artisan make:integration Slack`

This will create a `Slack` folder in your `Integrations` folder with an integration manager `Slack` and `SlackIntegrationServiceProvider`.

Define a unique key in `App\Integration\Slack`

```php
protected static string $integrationKey = 'slack';
```

You can also edit the following functions:
```php

// Override default rules for integration model
public function rules()
{
    return [];
}

// Set settings rules
public function settingRules()
{
    return [];
}

// Default values for settings
public function getDefaultSettings(): array
{
    return [];
}

// Set definition for integration to retrieve this information through an api
public function getDefinitions(): array
{
    return [
        'title' => 'Slack',
        'key' => self::getIntegrationKey(),
        'logo_url' => '',
        'description' => '',
    ];
}
```

Register this integration in `App\Integration\SlackServiceProvider`
```php
integrations()->registerIntegration(new Slack());
```

## Listen to hooks - event

You can listen to events of your application and do action based on the event which was fired.

In your `boot` method of `SlackServiceProvider` you could do

```php
Event::listen(Event::class, Listener::class);
```

The listener is responsive for the doing things which should happen when an event is fired.

## Listen to hooks - pipes

Another concept are pipelines with passables and pipes. Think of it as synchronous events with a return value.

For example if you are sending E-Mails in your application, you can make it possible for integration to hook into this process and add more text.

```php
Pipeline::pipe(ConstructingEmailPassable::class, AddSlackText::class);

// In AddSlackText
public function handle(ConstructingEmailPassable $passable, $next)
{
    // Check if booking has zoom link
    $passable->text .= "Something to add";

    return $next($passable);
}

// Somewhere in your application
pipeline(new ConstructingEmailPassable($text));
```

If you want to use pipelines you need to add the laravel-pipes package

```
composer require bddy/laravel-pipes
```

For more information look at [https://packagist.org/packages/bddy/laravel-pipes](https://packagist.org/packages/bddy/laravel-pipes)

## Handling errors and failures

This package helps you with handling error and failures with integrations. An error is a fatal error after which an integration cannot proceed further and need some action from a user. A failure ist a failed job which can be retried by a user.

When a fatal error occurs you can save the error with

```php
$manager->saveError($errorMessage, $exception);
```

After this the integration is in a kind of error state and the user need to resolve the error.

When the user resolves the error you can do

```php
$manager->removeError();
```

To check if an integration has an error you can use

```php
$manager->hasError();
```

### Failures

Failures informs the user that something went wrong. Basically it is a failed job which will be saved to the database. The user can then resolve the failure.

```php
// save failure
$manager->saveFailure($job, $exception, $contextKey, $displayName, $explanation);
// list all failures
$manager->listFailures();
// find failure by uuid
$manager->findFailure($uuid);
// retry failure by uuid
$manager->retryFailure($uuid);
// forget failure by uuid
$manager->forgetFailure($uuid);
// forget all failures
$manager->flushFailure();
```

## Config

We provide a `integrations.php` file where you can put you integration specific configs.

## Make commands

There are a variety of make commands for integration in form of

`php artisan integration:make:Z Integration Name`

for using normal laravel make command in an integration environment.

```php artisan integration:make:job Slack SentSlackMessage```

creates a laravel job `SentSlackMessage` in `App\Integrations\Slack\Jobs`.

## Getting models and manager

**Get a manager by key**

```php
integrations()->getIntegrationManager($key);
```

**Get key from integration model**

```php
$integration->getIntegrationKey();
```

**Get manager from model**
```php
$integration->manager();
```

**Get a specific manager without integration model**

```php
Slack::get();
```

**Get a specific manager with integration model**

```php
Slack::get()->for($integration);
```

## More useful methods

**initialize integration**

```php
$integration->initializeIntegration();
```

**activate and deactivate integration**

```php
$integration->activateIntegration();
$integration->deactivateIntegration();
```

### For corresponding which has relation to integrations
**Check if it has an active integration**

```php
$model->hasActiveIntegration($manager);
```

**Check if model has specific integration**
```php
$model->hasIntegration($manager);
```

**Get first specific integration**
```php
$model->getIntegration($manager);
```