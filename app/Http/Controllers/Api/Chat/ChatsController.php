<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\Chat;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Transformers\ChatTransformer;
use App\Http\Controllers\Api\Controller;

class ChatsController extends Controller
{
    /**
     * Store newly created chat with receiver.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     *
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $isToGroup = $request->receiver_type === 'group';

        $this->validate($request, [
            'message' => 'required|max:200',
            'receiver_type' => 'required|in:user,group',
            'receiver_id' => [
                'required',
                Rule::exists($isToGroup ? 'groups' : 'users', 'id')
                    ->where(function ($query) use ($user, $isToGroup) {
                        if (! $isToGroup) {
                            $query->where('id', '!=', $user->id);
                        }
                    }),
            ],
        ]);

        if ($isToGroup) {
            $receiver = Group::findOrFail($request->receiver_id);

            if (! $receiver->participants()->find($user->id)) {
                abort(403, 'You don\'t belongs to this group.');
            }
        } else {
            $receiver = User::findOrFail($request->receiver_id);

            // Validate user is allowed to send message to the receiver or not.
        }

        /** @var \App\Models\Chat $chat */
        $chat = $receiver->chats()->save((new Chat)->forceFill([
            'message' => $request->message,
            'sender_id' => $user->id,
        ]));

        $chat = $this->preprocessResource($chat, ChatTransformer::class);

        return ChatTransformer::make($chat);
    }
}
