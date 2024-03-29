<?php

namespace App\Events\Chats;

use App\Models\Chat;
use App\Models\User;
use App\Events\Event;
use App\Models\Group;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
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
     * The name of the queue on which to place the event.
     *
     * @var string
     */
    public $queue = 'high';

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Chat  $chat
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;

        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'chat' => $this->chat->load('sender', 'receivable'),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $senderId = $this->chat->sender_id;
        $receivableId = $this->chat->receivable_id;

        if ($this->chat->receivable instanceof Group) {
            return new PrivateChannel('chats.groups.' . $receivableId);
        } elseif ($this->chat->receivable instanceof User) {
            return new PrivateChannel('chats.users.' . $receivableId . '.to.' . $senderId);
        }

        return [];
    }
}
