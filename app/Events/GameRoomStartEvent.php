<?php

namespace App\Events;

use App\Cache\GameRoomCache;
use App\Helpers\Games\CardDeck;
use App\Helpers\Games\Poker;
use App\Models\Account;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\GamePlayerChip;
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
use Illuminate\Support\Facades\DB;

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
        if ($this->gameRoom->parameters->players_count == $this->players->count()) {
            GameRoomCache::clearGameRoomCache($this->roomId);
        }

        GamePlayerChip::where('game_room_id', $this->roomId)->delete();
        if (GameRoomCache::getActionIndex($this->roomId)) {
            return false;
        }

        if ($this->gameRoom->parameters->players_count == 2) {
            $shouldStart = $this->gameRoom->parameters->players_count == $this->players->count();
        } elseif ( $this->gameRoom->parameters->players_count == 6) {
            $shouldStart = $this->gameRoom->parameters->players_count - $this->players->count() <= 3;
        } elseif ($this->gameRoom->parameters->players_count == 9) {
            $shouldStart = $this->gameRoom->parameters->players_count - $this->players->count() <= 2;
        }

        if ($shouldStart ?? false) {
            return true;
        } else {
            GameRoomCache::setRound($this->roomId, 0);
        }
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        // remove previos chat messages.
        ChatMessage::where('room_id', $this->roomId)->delete();
        $this->initPlayersBet();
        $this->gameRoom->players_bet = $this->gameRoom->playersBet()->get()->keyBy('user_id');
        GameRoomPlayer::where('game_room_id', $this->roomId)->update([
            'is_playing' => 1,
        ]);

        return [
            'room_id' => $this->roomId,
            'players' => $this->players,
            'game_room' => GameRoomCache::getGameRoomCache($this->roomId)
        ];
    }

    private function initPlayersBet()
    {
        $previousSmallBlindIndex = GameRoomCache::getPreviousSmallBlindIndex($this->roomId);
        $smallBlindIndex = $previousSmallBlindIndex === null ? ($this->players->count() == 2 ? 0 : 1) : $this->rotate($previousSmallBlindIndex);
        $bigBlindIndex = $previousSmallBlindIndex === null ? ($this->players->count() == 2 ? 1 : 0) : $this->rotate($smallBlindIndex);
        $this->setGameRoomCache($smallBlindIndex, $bigBlindIndex);

        $deck = new CardDeck(explode(',', $this->provablyFairGame->secret));
        $deck->cut($this->provablyFairGame->shift_value % 52);
        $poker = new Poker($deck);
        $poker->addPlayers($this->gameRoom->parameters->players_count)->deal(2, 3)->play();
        GameRoomCache::setCommunityCard($this->roomId, $poker->getCommunityCards()->map->code);
    }

    private function rotate($smallBlindIndex)
    {
        $players = $this->players->pluck('user_id')->toArray();
        $rotateKey = null;
        foreach ($players as $key => $player) {
            if ($rotateKey === null) {
                $rotateKey = $key;
            }

            if ($smallBlindIndex < $key) {
                return $key;
            }
        }

        return $rotateKey;
    }

    private function setGameRoomCache($smallBlindIndex, $bigBlindIndex )
    {
        GameRoomCache::setPreviousSmallBlindIndex($this->roomId, $smallBlindIndex);
        GameRoomCache::setSmallBlind($this->roomId, $this->players[$smallBlindIndex]->user_id);
        GameRoomCache::setBigBlind($this->roomId, $this->players[$bigBlindIndex]->user_id);
        GameRoomCache::setEndPlayer($this->roomId, $this->players[$bigBlindIndex]->user_id);
        if ($this->players->count() == 2) {
            GameRoomCache::setDealer($this->roomId, $this->players[$smallBlindIndex]->user_id);
        } else {
            if ($smallBlindIndex > 0) {
                $dealerIndex = $smallBlindIndex - 1;
            } else {
                $dealerIndex =0;
            }

            GameRoomCache::setDealer($this->roomId, $this->players[$dealerIndex]->user_id);
        }

        GameRoomCache::setRound($this->roomId,  1);

        GameRoomCache::setActionIndex($this->roomId, $this->players->count() <= ($bigBlindIndex + 1) ? $this->players[0]->user_id : $this->players[$bigBlindIndex + 1]->user_id);
        GameRoomCache::setPlayerCanCheck($this->roomId, $this->players->count() <= ($bigBlindIndex + 1)  ? $this->players[0]->user_id : $this->players[$bigBlindIndex + 1]->user_id);

        GameRoomCache::setBet($this->roomId, $this->players[$smallBlindIndex]->user_id, $this->gameRoom->parameters->bet->small);
        GameRoomCache::setBet($this->roomId, $this->players[$bigBlindIndex]->user_id, $this->gameRoom->parameters->bet->big);
        GameRoomCache::setPreviouslyBet($this->roomId,$this->gameRoom->parameters->bet->big);
        GameRoomCache::setPot($this->roomId, ($this->gameRoom->parameters->bet->small + $this->gameRoom->parameters->bet->big));
        $this->deduceSmallAndBigBlindBuyIn($this->players[$smallBlindIndex]->user_id, $this->players[$bigBlindIndex]->user_id, $this->gameRoom->parameters->bet);
    }

    private function deduceSmallAndBigBlindBuyIn($smallUserId, $bigUserId, $bet)
    {
        Account::where('user_id', $smallUserId)->update([
            'buy_in' => DB::raw("buy_in - $bet->small")
        ]);

        Account::where('user_id', $bigUserId)->update([
            'buy_in' => DB::raw("buy_in - $bet->big")
        ]);

        broadcast(new OnPlayersEvent($this->players->toJson(), $this->roomId));
    }
}
