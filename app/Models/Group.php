<?php

namespace App\Models;

use App\Models\Concerns\Chatable;
use App\Models\Concerns\HasCreator;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\DeviceTokenable;

class Group extends Model
{
    use Chatable,
        HasCreator,
        DeviceTokenable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the group participants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
