<?php

namespace App\Events;

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

class GameRoomCommunityCardEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $players;
    public $roomId;
    public $gameRoom;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($roomId)
    {
        $this->roomId = $roomId;
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
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        // if all players have bet => show community card
        $gameRoomPlayersBetCount = GameRoomPlayerBet::where('game_room_id',$this->roomId)->count();
        $this->gameRoom = GameRoom::where('id', $this->roomId)->first();
        return $this->gameRoom->parameters->players_count == $gameRoomPlayersBetCount;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $communityCard = $this->gameRoom->communityCard()->first();
        return [
            'room_id' => $this->roomId,
            'community_card' => $communityCard,
        ];
    }
}
