<?php

namespace Anny\Integrations\Console\Commands\JsonApi;

/**
 * Class MakeValidators
 *
 * @package CloudCreativity\LaravelJsonApi
 */
class MakeValidators extends AbstractGeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:integration:json-api:validators';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new JSON API resource validator provider';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Validators';

    /**
     * Whether the resource type is non-dependent on eloquent
     *
     * @var boolean
     */
    protected $isIndependent = true;
}