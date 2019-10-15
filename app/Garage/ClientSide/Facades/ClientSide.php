<?php

namespace App\Garage\ClientSide\Facades;

use Illuminate\Support\Facades\Facade;
use App\Garage\ClientSide\ClientSide as ClientSideImpl;

/**
 * @method static array  share(string|\Closure $key, mixed $value = null)
 * @method static array  shareIfAuth(string|\Closure $key, mixed $value = null)
 * @method static array  getData()
 * @method static array  toArray()
 * @method static array  jsonSerialize()
 *
 * @see \App\Garage\ClientSide\ClientSide
 */
class ClientSide extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ClientSideImpl::class;
    }
}
