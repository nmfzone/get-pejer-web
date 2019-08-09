<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
    ];

    /**
     * Get all of the groups that are assigned this token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function groups()
    {
        return $this->morphedByMany(Group::class, 'tokenable');
    }

    /**
     * Get all of the users that are assigned this token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'tokenable');
    }
}
