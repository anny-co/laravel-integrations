<?php

namespace Bddy\Integrations\Console\Commands\JsonApi;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Class MakeResource
 *
 * @package CloudCreativity\LaravelJsonApi
 */
class MakeResource extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "make:integration:json-api:resource
        {integration : The integration to use.}
        {resource : The resource to create files for.}
        {api? : The API that the resource belongs to.}
        {--a|auth : Generate a resource authorizer.}
        {--c|content-negotiator : Generate a resource content negotiator.}
        {--e|eloquent : Use Eloquent classes.}
        {--N|no-eloquent : Do not use Eloquent classes.}
        {--o|only= : Specify the classes to generate.}
        {--x|except= : Skip the specified classes.}
    ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full JSON API resource.';

    /**
     * The available generator commands.
     *
     * @var array
     */
    private $commands = [
        'make:integration:json-api:adapter' => MakeAdapter::class,
        'make:integration:json-api:schema' => MakeSchema::class,
        'make:integration:json-api:validators' => MakeValidators::class,
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resourceParameter = [
            'resource' => $this->argument('resource'),
            'integration' => $this->argument('integration'),
            'api' => $this->argument('api'),
        ];

        $eloquentParameters = array_merge($resourceParameter, [
            '--eloquent' => $this->option('eloquent'),
            '--no-eloquent' => $this->option('no-eloquent'),
        ]);

        $commands = collect($this->commands);

        /** Just tell the user, if no files are created */
        if ($commands->isEmpty()) {
            $this->info('No files created.');
            return 0;
        }

        /** Filter out any commands the user asked os to. */
        if ($this->option('only') || $this->option('except')) {
            $type = $this->option('only') ? 'only' : 'except';

            $commands = $this->filterCommands($commands, $type);
        }

        /** Run commands that cannot accept Eloquent parameters. */
        $notEloquent = ['make:integration:json-api:validators'];

        if (!$this->runCommandsWithParameters($commands->only($notEloquent), $resourceParameter)) {
            return 1;
        }

        /** Run commands that can accept Eloquent parameters. */
        $eloquent = ['make:integration:json-api:adapter', 'make:integration:json-api:schema'];

        if (!$this->runCommandsWithParameters($commands->only($eloquent), $eloquentParameters)) {
            return 1;
        }

        /** Authorizer */
        if ($this->option('auth')) {
            $this->call('make:integration:json-api:authorizer', [
                'integration' => $this->argument('integration'),
                'name' => $this->argument('resource'),
                'api' => $this->argument('api'),
                '--resource' => true,
            ]);
        }

        /** Content Negotiator */
        if ($this->option('content-negotiator')) {
            $this->call('make:integration:json-api:content-negotiator', [
                'integration' => $this->argument('integration'),
                'name' => $this->argument('resource'),
                'api' => $this->argument('api'),
                '--resource' => true,
            ]);
        }

        /** Give the user a digial high-five. */
        $this->comment('All done, keep doing what you do.');

        return 0;
    }

    /**
     * Filters out commands using either 'except' or 'only' filter.
     *
     * @param Collection $commands
     * @param string $type
     * @return Collection
     */
    private function filterCommands(Collection $commands, $type)
    {
        $baseCommandName = 'make:json-api:';
        $filterValues = explode(',', $this->option($type));

        $targetCommands = collect($filterValues)
            ->map(function ($target) use ($baseCommandName) {
                return $baseCommandName . strtolower(trim($target));
            });

        return $commands->{$type}($targetCommands->toArray());
    }

    /**
     * Runs the given commands and passes them all the given parameters.
     *
     * @param Collection $commands
     * @param array $parameters
     * @return bool
     */
    private function runCommandsWithParameters(Collection $commands, array $parameters)
    {
        foreach ($commands->keys() as $command) {
            if (0 !== $this->call($command, $parameters)) {
                return false;
            }
        }

        return true;
    }

}