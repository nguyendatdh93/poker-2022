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

class GameRoomStartEvent implements ShouldBroadcast
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
        $this->gameRoom = GameRoom::where('id', $this->roomId)->first();
        $this->players = GameRoomPlayer::where('game_room_id', $this->roomId)->orderBy('id', 'asc')->get();
        return $this->gameRoom->parameters->players_count == $this->players->count();
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $this->initPlayersBet();
        $this->gameRoom->players_bet = $this->gameRoom->playersBet()->get()->keyBy('user_id');
        return [
            'room_id' => $this->roomId,
            'players' => $this->players,
            'game_room' => json_encode($this->gameRoom),
        ];
    }

    private function initPlayersBet()
    {
        $smallBlindIndex = $this->players->count() == 2 ? 0 : 1;
        $bigBlindIndex = $this->players->count() == 2 ? 1 : 2;
        $this->updateOrCreatePlayerBet($this->players[$smallBlindIndex]->user_id, $this->roomId, $this->gameRoom->parameters->bet);
        $this->updateOrCreatePlayerBet($this->players[$bigBlindIndex]->user_id, $this->roomId, $this->gameRoom->parameters->bet * 2);
        foreach ($this->players as $key => $player) {
            if ($key > $bigBlindIndex) {
                $this->updateOrCreatePlayerBet($this->players[$bigBlindIndex]->user_id, $this->roomId, 0);
            }
        }

        foreach ($this->players as $key => $player) {
            if ($key < $smallBlindIndex) {
                $this->updateOrCreatePlayerBet($player->user_id, $this->roomId, 0);
                continue;
            }

            break;
        }

    }

    private function updateOrCreatePlayerBet($playerId, $roomId, $bet)
    {
        GameRoomPlayerBet::updateOrCreate([
            'game_room_id' => $roomId,
            'user_id' => $playerId,
        ], [
            'bet' => $bet,
        ]);
    }
}
