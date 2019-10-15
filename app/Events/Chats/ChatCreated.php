<?php

namespace App\Events\Chats;

use App\Models\Chat;
use App\Events\Event;
use App\Models\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatCreated extends Event implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The chat data.
     *
     * @var \App\Models\Chat
     */
    public $chat;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Chat  $chat
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->chat->receivable instanceof Group) {
            return new PrivateChannel('chats.groups.' . $this->chat->receivable_id);
        }

        return new PrivateChannel('chats.users.' . ($this->chat->sender_id + $this->chat->receivable_id));
    }
}
