<?php

namespace App\Models\Concerns;

use App\Models\Chat;

trait Chatable
{
    /**
     * Get the model's chats.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function chats()
    {
        return $this->morphMany(Chat::class, 'receivable');
    }
}
