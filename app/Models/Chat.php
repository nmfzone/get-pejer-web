<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
    ];

    /**
     * Get the entity of senderable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function senderable()
    {
        return $this->morphTo();
    }

    /**
     * Get the entity of receivable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function receivable()
    {
        return $this->morphTo();
    }
}
