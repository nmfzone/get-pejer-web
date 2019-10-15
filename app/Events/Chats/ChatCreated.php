<?php

namespace App\Events\Chats;

use App\Models\Chat;
use App\Models\User;
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
        $channels = [];

        if ($this->chat->receivable instanceof Group) {
            $this->chat->receivable->participants->each(function ($participant) use ($channels) {
                array_push($channels, new PrivateChannel('chats.all.' . $participant->id));
            });

            $channels = array_merge($channels, [
                new PrivateChannel('chats.groups.' . $receivableId),
            ]);
        } elseif ($this->chat->receivable instanceof User) {
            $channels = array_merge($channels, [
                new PrivateChannel('chats.all.' . $senderId),
                new PrivateChannel('chats.all.' . $senderId),
                new PrivateChannel('chats.users.' . $senderId . '.to.' . $receivableId),
                new PrivateChannel('chats.users.' . $receivableId . '.to.' . $senderId),
            ]);
        }

        return $channels;
    }
}
