<?php

namespace Anny\Integrations\Console\Commands\JsonApi;

/**
 * Class MakeSchema
 *
 * @package CloudCreativity\LaravelJsonApi
 */
class MakeSchema extends AbstractGeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:integration:json-api:schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new JSON API resource schema';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Schema';
}