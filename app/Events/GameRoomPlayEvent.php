<?php

namespace App\Events;

use App\Cache\GameRoomCache;
use App\Helpers\Games\CardDeck;
use App\Helpers\Games\Poker;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\GameRoom;
use App\Models\GameRoomCommunityCard;
use App\Models\GameRoomPlayer;
use App\Models\GameRoomPlayerBet;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameRoomPlayEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bet;
    public $roomId;
    public $gameRoom;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($roomId, $bet)
    {
        $this->roomId = $roomId;
        $this->bet = $bet;
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
            'game_room' => GameRoomCache::getGameRoomCache($this->roomId)
        ];
    }
}
