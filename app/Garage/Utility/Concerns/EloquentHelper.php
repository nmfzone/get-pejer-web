<?php

namespace App\Garage\Utility\Concerns;

use Closure;

trait EloquentHelper
{
    /**
     * Generate unique value for the given column of model.
     *
     * @param  mixed  $model
     * @param  \Closure  $generate
     * @param  string  $column
     * @return string
     *
     * @throws \Exception
     */
    public static function generateUnique($model, Closure $generate, $column = 'id')
    {
        /** @var \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model */
        if (is_string($model)) {
            $model = app()->make($model);
        }

        $code = $generate();

        while (! is_null($model->find($code, $column))) {
            $code = $generate();
        }

        return $code;
    }
}
