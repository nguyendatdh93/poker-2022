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

class GameRoomStartEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $players;
    public $roomId;
    public $gameRoom;
    public $provablyFairGame;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($roomId, $provablyFairGame)
    {
        $this->roomId = $roomId;
        $this->provablyFairGame = $provablyFairGame;
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
            'game_room' => GameRoomCache::getGameRoomCache($this->roomId)
        ];
    }

    private function initPlayersBet()
    {
        $smallBlindIndex = $this->players->count() == 2 ? 0 : 1;
        $bigBlindIndex = $this->players->count() == 2 ? 1 : 2;
        $this->setGameRoomCache($smallBlindIndex, $bigBlindIndex);

        $deck = new CardDeck(explode(',', $this->provablyFairGame->secret));
        $deck->cut($this->provablyFairGame->shift_value % 52);
        $poker = new Poker($deck);
        $poker->addPlayers($this->gameRoom->parameters->players_count)->deal(2, 3)->play();
        GameRoomCache::setCommunityCard($this->roomId, $poker->getCommunityCards()->map->code);
    }

    private function setGameRoomCache($smallBlindIndex, $bigBlindIndex )
    {
        GameRoomCache::setSmallBlind($this->roomId, $this->players[$smallBlindIndex]->user_id);
        GameRoomCache::setBigBlind($this->roomId, $this->players[$bigBlindIndex]->user_id);
        GameRoomCache::setEndPlayer($this->roomId, $this->players[$bigBlindIndex]->user_id);
        if ($smallBlindIndex > 0) {
            GameRoomCache::setDealer($this->roomId, $this->players[0]->user_id);
        }

        GameRoomCache::setRound($this->roomId,  1);

        GameRoomCache::setPlayers($this->roomId, $this->players->pluck('user_id')->toArray());

        GameRoomCache::setActionIndex($this->roomId, $this->players->count() <= 3 ? $this->players[0]->user_id : $this->players[3]->user_id);

        GameRoomCache::setBet($this->roomId, $this->players[$smallBlindIndex]->user_id, $this->gameRoom->parameters->bet);
        GameRoomCache::setBet($this->roomId, $this->players[$bigBlindIndex]->user_id, $this->gameRoom->parameters->bet * 2);
        GameRoomCache::setPreviouslyBet($this->roomId,$this->gameRoom->parameters->bet * 2);
        GameRoomCache::setPot($this->roomId, $this->gameRoom->parameters->bet * 3);
    }
}
