<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\Chat;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Transformers\ChatTransformer;
use App\Http\Controllers\Api\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class UserChatsController extends Controller
{
    /**
     * Display chat conversations for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $chats = Chat::query()
            ->fromSub(function (QueryBuilder $query) use ($user) {
                return $query->rawQuery(
                    Chat::query()
                        ->selectRaw('id, receivable_id, receivable_type')
                        ->recentEachGroup($user, ['sender_id', 'receivable_id'])
                        ->groupBy(['id', 'receivable_id', 'receivable_type']) // not sure why
                        ->toRawSql()
                );
            }, 'grouped_chats')
            ->join('chats', 'grouped_chats.id', '=', 'chats.id')
            ->with('sender', 'receivable')
            ->latest()
            ->orderByDesc('chats.id');

        $chats = $this->preprocessResource($chats, ChatTransformer::class);

        return ChatTransformer::collection($chats->paginate());
    }

    /**
     * Display chat conversations with the given receivable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $receivableType
     * @param  int  $receivable
     * @return mixed
     */
    public function withReceivable(Request $request, $receivableType, $receivable)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (! in_array($receivableType, ['users', 'groups'], true)) {
            abort(404);
        }

        $isGroup = $receivableType === 'groups';

        if ($isGroup) {
            $receivable = Group::findOrFail($receivable);

            $query = Chat::query()
                ->where(function (Builder $query) use ($user, $receivable) {
                    $query->whereHasMorph(
                        'receivable',
                        Group::class,
                        function (Builder $query) use ($user) {
                            $query->whereHas('participants', function (Builder $query) use ($user) {
                                $query->where('user_id', $user->id);
                            });
                        }
                    )
                    ->where('receivable_id', $receivable->id);
                });
        } else {
            $receivable = User::where('id', '!=', $user->id)->findOrFail($receivable);

            $query = Chat::query()
                ->where(function (Builder $query) use ($user, $receivable) {
                    $query->where('sender_id', $user->id)
                        ->whereHasMorph('receivable', User::class)
                        ->where('receivable_id', $receivable->id);
                })
                ->orWhere(function (Builder $query) use ($user, $receivable) {
                    $query->where('sender_id', $receivable->id)
                        ->whereHasMorph('receivable', User::class)
                        ->where('receivable_id', $user->id);
                });
        }

        $query = $query->latest()
            ->orderByDesc('chats.id')
            ->with('sender', 'receivable');

        $chats = $this->preprocessResource($query, ChatTransformer::class);

        return ChatTransformer::collection($chats->paginate());
    }
}
