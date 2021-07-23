<?php

namespace App\Events;

use App\Cache\GameRoomCache;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\GameRoomPlayerFold;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FoldEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($room, $user)
    {
        $this->room = $room;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('game.' . $this->room);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $foldPlayers = GameRoomPlayerFold::where('game_room_id', $this->room)->get()->keyBy('user_id');
        return [
            'players' => $foldPlayers,
            'room_id' => $this->room,
            'user_id' => $this->user,
            'game_room' => GameRoomCache::getGameRoomCache($this->room)
        ];
    }
}
