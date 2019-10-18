<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserOnline extends Event implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user data.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The name of the queue on which to place the event.
     *
     * @var string
     */
    public $queue = 'high';

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('users.online.' . $this->user->id);
    }
}
