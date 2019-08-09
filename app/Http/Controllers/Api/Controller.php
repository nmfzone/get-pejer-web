<?php

namespace App\Http\Controllers\Api;

use Exception;
use InvalidArgumentException;
use App\Transformers\Transformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Garage\ApiTools\EloquentBuilderTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Garage\ApiTools\ApiController as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Controller extends BaseController
{
    use DispatchesJobs,
        ValidatesRequests,
        AuthorizesRequests,
        EloquentBuilderTrait;

    protected function preprocessResource($resource, $transformer = null)
    {
        $transformer = $this->makeTransformer($transformer);
        $resourceOptions = $this->parseResourceOptions();

        extract($resourceOptions);

        if (! isset($includes)) {
            $includes = [];
        }

        $includes = $this->getIncludes($includes, $transformer);

        if (($resource instanceof Model && $resource->exists) || $resource instanceof EloquentCollection) {
            $resource = $resource->load($includes);
        } elseif ($resource instanceof Builder ||
            $resource instanceof Relation ||
            ($resource instanceof Model && ! $resource->exists)) {
            $resourceOptions['includes'] = $includes;
            $this->applyResourceOptions($resource, $resourceOptions);
        }

        return $resource;
    }

    private function getIncludes($includes, $transformer = null)
    {
        if (! is_array($includes)) {
            throw new InvalidArgumentException('Includes should be an array.');
        }

        if ($transformer instanceof Transformer) {
            $allowed = (array) $transformer->includes;

            if (! in_array('*', $allowed)) {
                $includes = array_filter($includes, function ($include) use ($allowed) {
                    return in_array($include, $allowed);
                });
            }
        }

        return $includes;
    }

    private function makeTransformer($transformer)
    {
        if ($transformer) {
            if (is_string($transformer)) {
                $transformer = new $transformer([]);
            }

            if (! $transformer instanceof Transformer) {
                throw new Exception('Transformer should extends App\Transformers\Transformer');
            }
        }

        return $transformer;
    }
}
