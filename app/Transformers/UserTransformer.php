<?php

namespace Modules\User\Transformers;

use App\Transformers\Transformer;

class UserTransformer extends Transformer
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'device_token' => $this->when(
                optional(Auth::user())->id === $this->id,
                $this->device_token
            ),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
