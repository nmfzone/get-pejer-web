<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Resources\MissingValue;

class ChatTransformer extends Transformer
{
    /**
     * The relations that allowed to includes.
     *
     * @var array
     */
    public $includes = [
        'sender',
        'receivable',
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
            'message' => $this->message,
            'sender_id' => $this->sender_id,
            'sender' => UserTransformer::make($this->whenLoaded('sender')),
            'receivable_id' => $this->receivable_id,
            'receivable_type' => $this->receivable_type,
            'receivable' => $this->transformReceivable(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }

    protected function transformReceivable()
    {
        $receivable = $this->whenLoaded('receivable');

        if (! $receivable instanceof MissingValue) {
            if ($receivable instanceof User) {
                return UserTransformer::make($receivable);
            } elseif ($receivable instanceof Group) {
                return GroupTransformer::make($receivable);
            }
        }

        return $receivable;
    }
}
