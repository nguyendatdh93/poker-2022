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
        $this->initCommunityCards();
        $this->gameRoom->players_bet = $this->gameRoom->playersBet()->get()->keyBy('user_id');
        $this->gameRoom->community_card = $this->gameRoom->communityCard()->first();
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
        foreach ($this->players as $key => $player) {
            if ($key == $smallBlindIndex) { // for small blind
                $this->updateOrCreatePlayerBet($player->user_id, $this->roomId, $this->gameRoom->parameters->bet);
            }

            if ($key == $bigBlindIndex) { // for big blind
                $this->updateOrCreatePlayerBet($player->user_id, $this->roomId, $this->gameRoom->parameters->bet * 2);
            }
        }
    }

    private function initCommunityCards()
    {
        $deck = new CardDeck(explode(',', $this->provablyFairGame->secret));
        $deck->cut($this->provablyFairGame->shift_value % 52);
        $poker = new Poker($deck);
        $poker->addPlayers($this->players->count())->deal(2, 3)->play();

        GameRoomCommunityCard::updateOrCreate([
           'game_room_id' => $this->roomId,
        ], [
            'cards' => $poker->getCommunityCards()->map->code,
        ]);
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
