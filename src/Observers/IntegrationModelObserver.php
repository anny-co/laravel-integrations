<?php

namespace Anny\Integrations\Observers;

use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Support\AbstractIntegrationManager;
use Illuminate\Database\Eloquent\Model;

class IntegrationModelObserver
{
    /**
     * @param IntegrationModel|Model $model
     * @param string                 $hook
     *
     * @return void
     */
    protected function callEventHook(IntegrationModel|Model $model, string $hook)
    {
        $manager = $model->getIntegrationManager();

        if (method_exists($manager, $hook))
        {
            $manager->for($model);
            $manager->$hook();
        }
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function retrieved(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'retrieved');
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function creating(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'creating');
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function created(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'created');

        $model->initializeIntegration();
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function updating(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'updating');
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function updated(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'updated');
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function saving(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'saving');

        if ($model->isDirty('active')) {
            if ($model->isActive()) {
                $model->activateIntegration();
            } else {
                $model->deactivateIntegration();
            }
        }
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function saved(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'saved');

        // Check if integration was activated or deactivated
        if($model->wasChanged('active')) {
            $manager = $model->getIntegrationManager();
           if($model->isActive()) {
               if(method_exists($manager, 'activated')) {
                   $manager->activated();
               }
           }else {
               if(method_exists($manager, 'deactivated')) {
                   $manager->deactivated();
               }
           }
        }
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function deleting(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'deleting');

        // If integration is active, deactivate first
        if ($model->isActive()) {
            $model->deactivateIntegration();
        }

        /** @var AbstractIntegrationManager $manager */
        $manager = $model->getIntegrationManager();
        $manager->flushFailures();
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function deleted(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'deleted');

        // after integration is deleted, it could be deactivated
        if($model->wasChanged('active')) {
            $manager = $model->getIntegrationManager();
            if(method_exists($manager, 'deactivated')) {
                $manager->deactivated();
            }
        }
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function restoring(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'restoring');
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function restored(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'restored');
    }

    /**
     * @param IntegrationModel|Model $model
     *
     * @return void
     */
    public function replicating(IntegrationModel|Model $model)
    {
        $this->callEventHook($model, 'replicating');
    }
}