<?php

namespace App\Models;

use App\Models\Concerns\Viewable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Chat extends Model
{
    use Viewable;

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

    /**
     * Get the latest entry for each group.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecentEachGroup(Builder $query, User $user) : Builder
    {
        return $query->joinSub(function (QueryBuilder $query) {
            return $query->from('chats')
                ->selectRaw('least(sender_id, receivable_id) as f1, ' .
                    'greatest(sender_id, receivable_id) as f2, ' .
                    'receivable_type as f3, ' .
                    'max(id) as f4')
                ->groupBy(DB::raw(
                    'least(sender_id, receivable_id), ' .
                    'greatest(sender_id, receivable_id), ' .
                    'receivable_type'
                ));
        }, 'sub_table', function (JoinClause $join) {
            $join->on(DB::raw('least(sender_id, receivable_id)'), '=', 'sub_table.f1')
                ->on(DB::raw('greatest(sender_id, receivable_id)'), '=', 'sub_table.f2')
                ->on('chats.receivable_type', '=', 'sub_table.f3')
                ->on('chats.id', '=', 'sub_table.f4');
        })
        ->where(function (Builder $query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhereHasMorph(
                    'receivable',
                    Group::class,
                    function (Builder $query) use ($user) {
                        $query->whereHas('participants', function (Builder $query) use ($user) {
                            $query->where('user_id', $user->id);
                        });
                    }
                )
                ->orWhere(function (Builder $query) use ($user) {
                    $query->whereHasMorph('receivable', User::class)
                        ->where('receivable_id', $user->id);
                });
        });
    }
}
