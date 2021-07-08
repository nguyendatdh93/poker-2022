<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TurnToPlay implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $turnToPlay;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ChatRoom $room, $message, $turnToPlay)
    {
        $this->room = $room;
        $this->turnToPlay = $turnToPlay;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->room->id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $user = User::find($this->turnToPlay);
        return array_merge(
            [
                'user' => $user->only('id', 'name', 'avatar_url'),
                'message' => $this->message,
                'turn_to_play' => $this->turnToPlay
            ], [
                'recipients' => []
            ]
        );
    }
}
