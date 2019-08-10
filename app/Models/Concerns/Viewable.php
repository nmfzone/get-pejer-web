<?php

namespace App\Models\Concerns;

use App\Models\User;

trait Viewable
{
    /**
     * Get the model's viewers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function viewers()
    {
        return $this->morphToMany(User::class, 'viewable')->withTimestamps();
    }
}
