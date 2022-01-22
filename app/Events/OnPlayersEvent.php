<?php

namespace App\Events;

use App\Cache\GameRoomCache;
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

class OnPlayersEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $players;
    public $roomId;
    public $leftPlayerId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($players, $roomId, $leftPlayerId = 0)
    {
        $this->players = $players;
        $this->roomId = $roomId;
        $this->leftPlayerId = $leftPlayerId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('game.' . $this->roomId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'room_id' => $this->roomId,
            'players' => $this->players,
            'left_player_id' => $this->leftPlayerId,
        ];
    }
}
