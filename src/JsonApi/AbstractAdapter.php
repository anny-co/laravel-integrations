<?php

namespace Anny\Integrations\JsonApi;

use Anny\Integrations\Contracts\IntegrationModel;
use CloudCreativity\LaravelJsonApi\Adapter\AbstractResourceAdapter;
use CloudCreativity\LaravelJsonApi\Contracts\Http\Query\QueryParametersInterface;
use CloudCreativity\LaravelJsonApi\Document\ResourceObject;
use CloudCreativity\LaravelJsonApi\Pagination\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

abstract class AbstractAdapter extends AbstractResourceAdapter
{
    /**
     * @return IntegrationModel|Model
     */
    public abstract function getIntegration(): IntegrationModel|Model|null;

    /**
     * Return a collection with all resources.
     *
     * @param array|null $filters
     * @param array|null $pagination
     *
     * @return Collection
     */
    public abstract function getResources(array|null $filters = null, array|null $pagination = null): Collection;

    /**
     * @inheritdoc
     */
    public function query(QueryParametersInterface|EncodingParametersInterface $parameters)
    {
        $integration = $this->getIntegration();

        if(!$integration) {
            return [];
        }

        // Get filters
        $filter = $parameters->getFilteringParameters();

        // Get pagination
        $pagination = $parameters->getPaginationParameters();

        // Get resources
        $resources = $this->getResources($filter, $pagination);

        // Filter
        $resources = $this->filter($resources, $filter);

        return $this->createPage($resources, $pagination);
    }

    /**
     * @param Collection $resources
     * @param array|null $filter
     *
     * @return Collection
     */
    public function filter(Collection $resources, ?array $filter = []): Collection
    {
        $ids = Arr::wrap(Arr::get($filter, 'ids'));

        if (count($ids) > 0) {
            $resources = $resources
                ->filter(function ($model) use ($ids) {
                    if(is_array($model) && array_key_exists('id', $model)) {
                        return in_array($model['id'], $ids);
                    }

                    if(is_object($model) && property_exists($model, 'id')){
                        return in_array($model->id, $ids);
                    }

                    return true;
                });
        }

        return $resources;
    }

    /**
     * @param Collection $resources
     * @param array|null $pagination
     *
     * @return Page
     */
    protected function createPage(Collection $resources, ?array $pagination = null, ?int $total = null): Page
    {
        $meta = null;
        $data = $resources->all();

        if ($pagination) {
            $page = (int) Arr::get($pagination, 'number', 1);
            $size = (int) Arr::get($pagination, 'size', 30);
            $total = $total ?? $resources->count();
            $from = ($page - 1) * $size;
            $to = min($page * $size, $total);

            // slice only if resource contains all entries instead of single page
            if (! $total) {
                $data = $resources->slice($from, $to - $from)->all();
            }

            // Make meta
            $meta = [
                'page' => [
                    'current-page' => $page,
                    'last-page' => ceil($total / $size),
                    'per-page' => $size,
                    'from' => $from,
                    'to' => $to,
                    'total' => $total,
                ],
            ];
        }

        return new Page(
            $data,
            null,
            null,
            null,
            null,
            $meta,
            null
        );
    }

    /*
     * TODO: implement this functions when possible
     */

    /**
     * @inheritdoc
     */
    public function exists(string $resourceId): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function find(string $resourceId)
    {
    }

    public function findMany(iterable $resourceIds): iterable
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function createRecord(ResourceObject $resource)
    {
    }

    /**
     * @inheritdoc
     */
    protected function fillAttributes($record, Collection $attributes)
    {
    }

    /**
     * @inheritdoc
     */
    protected function persist($record)
    {
    }

    /**
     * @inheritdoc
     */
    protected function destroy($record)
    {
    }
}