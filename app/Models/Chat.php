<?php

namespace App\Models;

use App\Models\Concerns\Viewable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasRecentGroupScope;

class Chat extends Model
{
    use Viewable,
        HasRecentGroupScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
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
     * Get the receivable entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function receivable()
    {
        return $this->morphTo();
    }
}
