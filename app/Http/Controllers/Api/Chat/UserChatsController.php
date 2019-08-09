<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\ChatTransformer;
use App\Http\Controllers\Api\Controller;

class UserChatsController extends Controller
{
    /**
     * Display chat histories for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $chats = Chat::query()
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->recentEachGroup(['sender_id', 'receiver_id'])
            ->with('sender', 'receiver')
            ->latest();

        $chats = $this->preprocessResource($chats, ChatTransformer::class);

        return ChatTransformer::collection($chats->paginate());
    }

    /**
     * Display chat history with the given sender.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $sender
     * @return mixed
     */
    public function withSender(Request $request, User $sender)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $chats = Chat::query()
            ->where(function ($query) use ($user, $sender) {
                $query->where('sender_id', $sender->id)
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($user, $sender) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $sender->id);
            })
            ->latest()
            ->with('sender', 'receiver');

        $chats = $this->preprocessResource($chats, ChatTransformer::class);

        return ChatTransformer::collection($chats->paginate());
    }
}
