<?php

namespace App\Transformers;

use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\Resource;

abstract class Transformer extends Resource
{
    /**
     * The relations that allowed to includes.
     *
     * @var array
     */
    public $includes = [];

    protected function conditional($value, callable $callback = null)
    {
        if (empty($value) || ($callback && ! $callback())) {
            return new MissingValue;
        }

        return $value;
    }
}
