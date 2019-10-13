<?php

namespace App\Garage\Utility;

use Ramsey\Uuid\Uuid;
use App\Garage\Utility\Concerns\FactoryHelper;
use App\Garage\Utility\Concerns\EloquentHelper;

class Helper
{
    use FactoryHelper,
        EloquentHelper;

    /**
     * Generate unique uuid for the given column of model.
     *
     * @param  string  $model
     * @param  string  $column
     * @param  int  $length
     * @param  bool  $withoutDash
     * @return string
     *
     * @throws \Exception
     */
    public static function generateUniqueUuid($model, $column = 'id', $length = 36, $withoutDash = false)
    {
        $generate = function () use ($length, $withoutDash) {
            $uuid = Uuid::uuid4()->toString();

            if ($withoutDash) {
                $uuid = str_replace('-', '', $uuid);
            }

            return substr($uuid, 0, $length);
        };

        return self::generateUnique($model, $generate, $column);
    }
}
