<?php

namespace App\Garage\ClientSide;

use Closure;
use JsonSerializable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class ClientSide implements Arrayable, JsonSerializable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $authData;

    /**
     * Create a new ClientSide instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->data = [];
        $this->authData = [];
    }

    /**
     * Share data to the client side.
     *
     * @param  string|\Closure  $key
     * @param  mixed  $value
     * @return void
     */
    public function share($key, $value = null): void
    {
        if ($key instanceof Closure) {
            $this->data[] = $key;
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * Share data to the client side if authenticated.
     *
     * @param  string|\Closure  $key
     * @param  mixed  $value
     * @return void
     */
    public function shareIfAuth($key, $value = null): void
    {
        if ($key instanceof Closure) {
            $this->authData[] = $key;
        } else {
            $this->authData[$key] = $value;
        }
    }

    /**
     * Transform Closure data to something displayable.
     *
     * @param  array  $data
     * @return array
     */
    protected function transformData(array $data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $result = array_merge($result, Arr::wrap(value($value)));
            } else {
                $result[$key] = value($value);
            }
        }

        return $result;
    }

    /**
     * Get the data that needs to share.
     *
     * @return array
     */
    public function getData(): array
    {
        $result = $this->transformData($this->data);

        if (Auth::check()) {
            $result = array_merge($result, $this->transformData($this->authData));
        }

        return $result;
    }

    /**
     * Get the collection of data as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->getData());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        }, $this->getData());
    }
}
