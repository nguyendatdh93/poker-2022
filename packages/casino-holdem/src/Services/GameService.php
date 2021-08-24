<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Packages\CasinoHoldem\Services;

use App\Cache\GameRoomCache;
use App\Events\ActionEvent;
use App\Events\CallEvent;
use App\Events\GameRoomPlayEvent;
use App\Events\RaiseEvent;
use App\Events\ChatMessageSent;
use App\Events\FoldEvent;
use App\Events\GameRoomCommunityCardEvent;
use App\Events\GameRoomStartEvent;
use App\Events\OnPlayersEvent;
use App\Helpers\Games\CardDeck;
use App\Helpers\Games\Poker;
use App\Helpers\Games\PokerPlayer;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Game;
use App\Models\GameRoom;
use App\Models\GameRoomCommunityCard;
use App\Models\GameRoomPlayer;
use App\Models\GameRoomPlayerBet;
use App\Models\GameRoomPlayerCard;
use App\Models\GameRoomPlayerFold;
use App\Models\User;
use App\Services\AccountService;
use App\Services\GameService as ParentGameService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Packages\CasinoHoldem\Helpers\PokerHand;
use Packages\CasinoHoldem\Models\CasinoHoldem;

class GameService extends ParentGameService
{
    protected $gameableClass = CasinoHoldem::class;
    protected $makeVisible = ['dealer_cards', 'dealer_hand', 'dealer_kickers', 'dealer_hand_rank'];

    public function makeSecret(): string
    {
        return implode(',', (new CardDeck())->toArray());
    }

    /**
     * Deal initial set of cards
     *
     * @param $params
     */
    public function deal($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load initially shuffled deck
        $deck = new CardDeck(explode(',', $provablyFairGame->secret));
        // cut the deck (provably fair)
        $deck->cut($provablyFairGame->shift_value % 52);

//      TESTING
//      -------
//      ROYAL FLUSH
//      $deck = $deck->replace(['HA','S2','HJ','C4','HQ','HK','HT','DA']);
//
//      2 ACES IN PLAYER POCKET
//      $deck = $deck->replace([0 => 'HA', 2 => 'DA']);
//
//        ---------------------------------------------------------------------

        $poker = new Poker($deck);
        $poker->addPlayers(2)->deal(2, 3)->play();

        $gameable = new $this->gameableClass();
        $gameable->deck = $deck->toArray();
        $gameable->ante_bet = $params['bet'];
        $gameable->bonus_bet = $params['bonus_bet'];
        $gameable->player_cards = $poker->getPlayer(1)->getPocketCards()->map->code;
        $gameable->community_cards = $poker->getCommunityCards()->map->code;
        $gameable->player_hand = $poker->getPlayer(1)->getHand()->get()->map->code;
        $gameable->player_hand_rank = $poker->getPlayer(1)->getHand()->getRank();

        $kickers = $poker->getPlayer(1)->getHand()->getKickers();
        if ($kickers->count() > 0) {
            $gameable->player_kickers = $kickers->map->code;
        }

        if ($gameable->bonus_bet > 0) {
            $bonusHand = new PokerHand($poker->getPlayer(1)->getPocketCards(), $poker->getCommunityCards());
            $gameable->player_bonus_hand = $bonusHand->get()->map->code;
            $gameable->player_hand_rank = $bonusHand->getRank();
        }

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;
        if ($params['is_big_blind'] ?? false) {
            $gameable->ante_bet = $gameable->ante_bet * 2;
        }

        $this->save([
                'bet' => $gameable->ante_bet + $gameable->bonus_bet,
                'win' => 0,
                'status' => Game::STATUS_IN_PROGRESS]
        );

        return $this;
    }

    /**
     * Fold
     *
     * @return GameService
     * @throws \Exception
     */
    public function fold($params): GameService
    {
        if (GameRoomCache::getFoldPlayer($params['room_id'], $params['user_id'])) {
            return $this;
        }

        GameRoomCache::setFoldPlayer($params['room_id'], $params['user_id']);
        GameRoomCache::setActionIndex($params['room_id'], $this->getNextActionIndex($params));
        $endPlayer = GameRoomCache::getEndPlayer($params['room_id']);
        if ($params['user_id'] == $endPlayer) {
            GameRoomCache::setEndPlayer($params['room_id'], $this->changeEndPlayer($params));
        }

        $this->nextRound($params['room_id'], $params['user_id']);
        $this->setPlayerCanCheck($params['room_id']);
        broadcast(new GameRoomPlayEvent($params['room_id'], $params['user_id'], 0));
        $this->sendNextPlayerActionMessage($params);
        return $this;
    }

    private function changeEndPlayer($params)
    {
        $players = GameRoomCache::getPlayers($params['room_id']);
        $foldPlayers = GameRoomCache::getFoldPlayers($params['room_id']);
        $playerId = $params['user_id'];
        $currentIndex = array_search($playerId, $players);
        $rotatePlayer = null;
        for ($i=count($players) - 1; $i>=0; $i--) {
            if (in_array($players[$i], $foldPlayers)) {
                continue;
            }

            if (!$rotatePlayer) {
                $rotatePlayer = $players[$i];
            }

            if ($currentIndex > $i) {
                return $players[$i];
            }
        }

        return $rotatePlayer;
    }

    private function getNextActionIndex($params)
    {
        $players = GameRoomCache::getPlayers($params['room_id']);
        $foldPlayers = GameRoomCache::getFoldPlayers($params['room_id']);
        $playerId = $params['user_id'];
        $currentIndex = array_search($playerId, $players);
        $rotatePlayer = null;
        foreach ($players as $key => $player) {
            if (in_array($player, $foldPlayers)) {
                continue;
            }

            if (!$rotatePlayer) {
                $rotatePlayer = $player;
            }

            if ($currentIndex < $key) {
                return $player;
            }
        }

        return $rotatePlayer;
    }

    /**
     * Call
     *
     * @return GameService
     * @throws \Exception
     */
    public function call($params): GameService
    {
        try {
            DB::beginTransaction();
            $this->setPlayerCanCheck($params['room_id']);
            $previouslyBet = GameRoomCache::getPreviouslyBet($params['room_id']);
            $this->handleBetAction($params, $previouslyBet);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this;
    }

    /**
     * Call
     *
     * @return GameService
     * @throws \Exception
     */
    public function check($params): GameService
    {
        try {
            DB::beginTransaction();
            GameRoomCache::setPlayerCanCheck($params['room_id'], $this->getNextPlayerCanCheck($params));
            $this->handleBetAction($params, 0);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this;
    }
    
    /**
     * Call
     *
     * @return GameService
     * @throws \Exception
     */
    public function raise($params): GameService
    {
        try {
            DB::beginTransaction();
            $this->setPlayerCanCheck($params['room_id']);
            $previouslyBet = GameRoomCache::getPreviouslyBet($params['room_id']);
            $bet = $previouslyBet * 2;
            $this->handleBetAction($params, $bet);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this;
    }

    /**
     * Call
     *
     * @return GameService
     * @throws \Exception
     */
    public function bet($params): GameService
    {
        try {
            DB::beginTransaction();
            $this->setPlayerCanCheck($params['room_id']);
            $previouslyBet = GameRoomCache::getPreviouslyBet($params['room_id']);
            $bet = $previouslyBet * 2;
            $this->handleBetAction($params, $bet);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this;
    }

    private function handleBetAction($params, $bet)
    {
        try {
            $account = Account::where('user_id', $params['user_id'])->first();
            $account->decrement('balance', abs($bet));

            AccountTransaction::create([
                'account_id' => $account->id,
                'amount' => -$bet,
                'balance' => $account->balance,
                'transactionable_type' => CasinoHoldem::class,
                'transactionable_id' => 1,
            ]);

            GameRoomCache::setActionIndex($params['room_id'], $this->getNextActionIndex($params));
            $this->nextRound($params['room_id'], $params['user_id']);
            GameRoomCache::setBet($params['room_id'], $params['user_id'], $bet);
            GameRoomCache::setPot($params['room_id'], GameRoomCache::getPot($params['room_id']) + $bet);
            if ($bet) {
                GameRoomCache::setPreviouslyBet($params['room_id'], $bet);
            }
            
            broadcast(new GameRoomPlayEvent($params['room_id'], $params['user_id'], $bet));
            $this->sendNextPlayerActionMessage($params);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error("handleBetAction: $message", [$e->getTraceAsString()]);
        }

    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000, 99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $minBonusBet = intval(config('casino-holdem.min_bonus_bet'));
        $maxBonusBet = intval(config('casino-holdem.max_bonus_bet'));

        $instance
            ->deal([
                'bet' => random_int($minBet ?: config('casino-holdem.min_bet'), $maxBet ?: config('casino-holdem.max_bet')),
                'bonus_bet' => random_int($minBonusBet, random_int(1, $maxBonusBet))
            ])
            ->unsetGameable();

        if (random_int(0, 1) == 0) {
            $instance->fold();
        } else {
            $instance->call();
        }
    }

    public function onPlayers($params)
    {
        $players = $params['players'];
        foreach ($players as $player) {
            if (GameRoomPlayer::where('user_id', $player['id'])->count() == 0) {
                GameRoomPlayer::create([
                    'game_room_id' => $params['room_id'],
                    'user_id' => $player['id'],
                ]);
            }
        }

        // Delete users who are no longer in the room
        if (array_column($players, 'id')) {
            GameRoomPlayer::whereNotIn('user_id', array_column($players, 'id'))->delete();
        }

        $this->getPlayersCard($params['room_id'], $players);
        $players = $this->getRoomPlayers($params);
        broadcast(new OnPlayersEvent($players->toJson(), $params['room_id']));
        broadcast(new GameRoomStartEvent($params['room_id'], $this->getProvablyFairGame()));
        return $this;
    }

    public function left($params)
    {
        $player = $params['player'];
        GameRoomPlayer::where('user_id', $player['id'])->delete();
        $players = $this->getRoomPlayers($params);
        if (GameRoomPlayer::where('game_room_id', $params['room_id'])->count() <= 1) {
            GameRoomCache::clearGameRoomCache($params['room_id']);
        }

        GameRoomCache::clearBet($params['room_id'], $player['id']);
        GameRoomCache::clearFoldPlayer($params['room_id'], $player['id']);

        broadcast(new OnPlayersEvent($players->toJson(), $params['room_id']));
        return $this;
    }

    public function communityCard($params)
    {
        $gameRoomCommunityCard = GameRoomCommunityCard::where('game_room_id', $params['room_id'])->first();
        if (!$gameRoomCommunityCard) {
            $gameRoom = GameRoom::where('id', $params['room_id'])->first();
            $provablyFairGame = $this->getProvablyFairGame();
            $deck = new CardDeck(explode(',', $provablyFairGame->secret));
            $deck->cut($provablyFairGame->shift_value % 52);
            $poker = new Poker($deck);
            $poker->addPlayers($gameRoom->parameters->players_count)->deal(2, 3)->play();

            GameRoomCommunityCard::updateOrCreate([
                'game_room_id' => $params['room_id'],
            ], [
                'cards' => $poker->getCommunityCards()->map->code,
            ]);
        }

        broadcast(new GameRoomCommunityCardEvent($params['room_id']));
        return $this;
    }

    public function action($params)
    {
        $gameRoom = GameRoom::where('id', $params['room_id'])->first();
        $playerIdsFold = GameRoomPlayerFold::where('game_room_id', $params['room_id'])
            ->get()
            ->pluck('user_id');
        $playerCanAction = GameRoomPlayerBet::where('game_room_id', $params['room_id'])
            ->where('round', $gameRoom->round)
            ->whereIn('bet', [null, 0])
            ->first();
        if (!$playerCanAction) { // finish round
            $playerCanAction = GameRoomPlayerBet::where('game_room_id', $params['room_id'])
                ->whereNotIn('user_id', $playerIdsFold)
                ->first();
        }

        broadcast(new ActionEvent($params['room_id'], $playerCanAction));
        return $this;
    }

    private function sendNextPlayerActionMessage($params)
    {
        $nextActionUserId = GameRoomCache::getActionIndex($params['room_id']);
        if ($nextActionUserId !== null && GameRoomCache::getRound($params['room_id']) <= 4) {
            $user = User::where('id', $nextActionUserId)->first() ?? null;
            if ($user) {
                $this->sendChatMessage($params['room_id'], $params['user_id'], "It is $user->name turn, you have 20 seconds to act");
            }
        }
    }

    private function getRoomPlayers($params)
    {
        $players = GameRoomPlayer::with([
            'gameRoomPlayerCards' => function($query) use ($params) {
                return $query->where('game_room_id', $params['room_id']);
            }
        ])->where('game_room_id', $params['room_id'])->orderBy('id', 'asc')->get();
        foreach ($players as $key => $player) {
            $players[$key]['cards'] = $player->gameRoomPlayerCards->first()->cards;
            $players[$key]['name'] = $player->user->name;
        }

        return $players;
    }

    private function getPlayersCard($roomID, $players)
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load initially shuffled deck
        $deck = new CardDeck(explode(',', $provablyFairGame->secret));
        // cut the deck (provably fair)
        $deck->cut($provablyFairGame->shift_value % 52);
        $poker = new Poker($deck);
        $poker->addPlayers(count($players))->deal(2, 3)->play();;

        foreach ($players as $key => $player) {
            if (Auth::id() == $player['id']) {
                $cards = $poker->getPlayer($key+1)->getPocketCards()->map->code;
                GameRoomPlayerCard::updateOrCreate([
                    'game_room_id' => $roomID,
                    'user_id' => $player['id'],
                ],[
                    'cards' => $cards
                ]);
            }
        }
    }

    private function nextRound($roomId, $playerId)
    {
        $endPlayer = GameRoomCache::getEndPlayer($roomId);
        if ($playerId == $endPlayer) {
            $this->handleForNextRound($roomId, $playerId);
        }
    }

    private function handleWinnerCards($roomId)
    {
        $playerIds = GameRoomCache::getPlayers($roomId);
        $playersCards = GameRoomPlayerCard::where('game_room_id', $roomId)->whereIn('user_id', $playerIds)->get();
        $winnerIndex = 0;
        $winner = new PokerHand(collect($playersCards[$winnerIndex]->cards), collect(GameRoomCache::getCommunityCard($roomId)));
        for ($i = 1; $i < $playersCards->count(); $i++) {
            $competitor = new PokerHand(collect($playersCards[$i]->cards), collect(GameRoomCache::getCommunityCard($roomId)));
            if ($winner->compare($competitor) == -1) {
                $winner = $competitor;
                $winnerIndex = $i;
            }
        }

        GameRoomCache::setWinner($roomId, $playersCards[$winnerIndex]->user_id);
        GameRoomCache::setWinnerCards($roomId, $playersCards[$winnerIndex]->cards);
        GameRoomCache::setPlayersCards($roomId, $playersCards->keyBy('user_id'));
        $user = User::where('id', $playersCards[$winnerIndex]->user_id)->first();
        $pot = GameRoomCache::getPot($roomId);
        $this->sendChatMessage($roomId, $playersCards[$winnerIndex]->user_id, "$user->name wins pot($pot) with high card king");
    }

    private function handleForNextRound($roomId, $playerId)
    {
        $round = GameRoomCache::getRound($roomId);
        GameRoomCache::setRound($roomId, $round + 1);
        if (in_array($round + 1, [3,4])) {
            $communityCard = GameRoomCache::getCommunityCard($roomId);
            $communityCard[] = $this->addCommunityCard();
            GameRoomCache::setCommunityCard($roomId, $communityCard);
        }

        if ($round + 1 <= 4) {
            $this->sendChatMessage($roomId, $playerId, 'Next to round '. GameRoomCache::getRound($roomId));
        } else {
            $this->handleWinnerCards($roomId);
        }

//        $this->setPlayerCanCheck($roomId);
    }

    private function getNextPlayerCanCheck($params)
    {
        $players = GameRoomCache::getPlayers($params['room_id']);
        $foldPlayers = GameRoomCache::getFoldPlayers($params['room_id']);
        $playerId = $params['user_id'];
        $currentIndex = array_search($playerId, $players);
        $rotatePlayer = null;
        foreach ($players as $key => $player) {
            if (in_array($player, $foldPlayers)) {
                continue;
            }

            if (!$rotatePlayer) {
                $rotatePlayer = $player;
            }

            if ($currentIndex < $key) {
                return $player;
            }
        }

        return $rotatePlayer;
    }

    private function setPlayerCanCheck($roomId)
    {
        $players = GameRoomCache::getPlayers($roomId);
        GameRoomCache::setPlayerCanCheck($roomId, count($players) <= 3 ? $players[0] : $players[3]);
    }

    private function addCommunityCard()
    {
        $deck = new CardDeck(explode(',', $this->getProvablyFairGame()->secret));
        $deck->cut($this->getProvablyFairGame()->shift_value % 52);
        $poker = new Poker($deck);
        $poker->deal(2, 1)->play();

        return $poker->getCommunityCards()->map->code->first();
    }

    private function sendChatMessage($roomId, $userId, $message)
    {
        // store message
        $chatRoom = ChatRoom::where('room_id', $roomId)->first();
        $chatMessage = ChatMessage::create([
            'room_id' => $chatRoom->id,
            'user_id' => $userId,
            'message' => $message,
            'sys' => 1,
        ]);

        broadcast(new ChatMessageSent($chatRoom, $chatMessage));
    }
}
