<?php

namespace App\Models;

use App\Models\Concerns\HasRecentGroupScope;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasRecentGroupScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'sender_id',
        'receiver_id',
    ];

    /**
     * Get the sender entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
