<?php

namespace Anny\Integrations\Console\Commands\JsonApi;

use Anny\Integrations\Console\Commands\AbstractGeneratorCommand;

class MakeResourceProvider extends AbstractGeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:integration:json-api:resource-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new {json:api} resource provider for an integration.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'IntegrationJsonApiResourceProvider';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return parent::getDefaultNamespace($rootNamespace) . '\\JsonApi';
    }

    /**
     * @inheritdoc
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/JsonApi/stubs/resource-provider.stub');
    }
}