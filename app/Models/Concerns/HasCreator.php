<?php

namespace App\Models\Concerns;

use App\Models\User;

trait HasCreator
{
    /**
     * Constrain the given query by the provided creator.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $creator
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCreator($query, $creator)
    {
        return $query->where('created_by', $creator);
    }

    /**
     * Get the model's creator.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
