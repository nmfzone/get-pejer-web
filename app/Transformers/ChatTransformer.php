<?php

namespace App\Transformers;

class ChatTransformer extends Transformer
{
    /**
     * The relations that allowed to includes.
     *
     * @var array
     */
    public $includes = [
        'sender',
        'receiver',
    ];

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
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'sender' => $this->whenLoaded('sender'),
            'receiver' => $this->whenLoaded('receiver'),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
