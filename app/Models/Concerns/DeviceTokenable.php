<?php

namespace App\Models\Concerns;

use App\Models\DeviceToken;

trait DeviceTokenable
{
    /**
     * Get all of the device tokens for the model's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function deviceTokens()
    {
        return $this->morphToMany(DeviceToken::class, 'device_tokenable')->withTimestamps();
    }
}
