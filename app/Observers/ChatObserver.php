<?php

namespace App\Observers;

use App\Models\Chat;
use App\Events\Chats\ChatCreated;
use App\Events\Chats\NotifyNewChat;

class ChatObserver
{
    /**
     * Listen to the Chat created event.
     *
     * @param \App\Models\Chat  $chat
     * @return void
     *
     * @throws \Exception
     */
    public function created(Chat $chat)
    {
        event(new ChatCreated($chat));
        event(new NotifyNewChat($chat));
    }
}
