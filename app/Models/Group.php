<?php

namespace App\Models;

use App\Models\Concerns\Chatable;
use App\Models\Concerns\Tokenable;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use Chatable,
        Tokenable;

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
     * Get the group users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
