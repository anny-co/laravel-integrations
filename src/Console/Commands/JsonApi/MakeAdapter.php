<?php

namespace Bddy\Integrations\Console\Commands\JsonApi;

/**
 * Class MakeAdapter
 *
 * @package CloudCreativity\LaravelJsonApi
 */
class MakeAdapter extends AbstractGeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:integration:json-api:adapter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new JSON API resource adapter';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Adapter';

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/adapter.stub');
    }
}