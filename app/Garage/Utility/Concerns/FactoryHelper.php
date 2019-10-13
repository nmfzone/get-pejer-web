<?php

namespace App\Garage\Utility\Concerns;

trait FactoryHelper
{
    /**
     * First or create model data if not exists.
     *
     * @param  string  $model
     * @param  array  $states
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Exception
     */
    public static function firstOrCreateData(string $model, array $states = [])
    {
        /** @var \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model */
        $model = app()->make($model);
        $data = $model::inRandomOrder();

        foreach ($states as $state) {
            $data = $data->$state();
        }

        $data = $data->first();

        return $data
            ? $data
            : factory($model)->states($states)->create();
    }
}
