<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Packages\CasinoHoldem\Services;

use App\Cache\GameRoomCache;
use App\Events\ActionEvent;
use App\Events\ResultEvent;
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
use App\Models\GamePlayerChip;
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

        $account = Account::where('user_id', $params['user_id'])->first();
        if ($account->buy_in <= 0) {
            return $this->left([
                'player' => [
                    'id' => $params['user_id']
                ],
                'room_id' => $params['room_id']
            ]);
        }

        GameRoomCache::setFoldPlayer($params['room_id'], $params['user_id']);
        GameRoomCache::setActionIndex($params['room_id'], $this->getNextActionIndex($params));
        $endPlayer = GameRoomCache::getEndPlayer($params['room_id']);
        if ($params['user_id'] == $endPlayer) {
            GameRoomCache::setEndPlayer($params['room_id'], $this->changeEndPlayer($params));
        }

        // if there is only one player in room. It should be check winner
        $foldPlayers = GameRoomCache::getFoldPlayers($params['room_id']);
        $players = GameRoomCache::getPlayers($params['room_id']);
        $leftPlayers = array_diff($players, $foldPlayers);;
        if (count($leftPlayers) == 1) {
            $this->handleWinnerCards($params['room_id'], reset($leftPlayers));
            sleep(3);
            $this->moveToNextGame($params['room_id'], $params['user_id']);
            return $this;
        }

        $this->nextRound($params['room_id'], $params['user_id']);
        broadcast(new GameRoomPlayEvent($params['room_id'], $params['user_id'], 0));
        $this->setPlayerCanCheck($params['room_id']);
        $this->sendNextPlayerActionMessage($params);
        return $this;
    }

    public function changeEndPlayer($params)
    {
        $players = GameRoomCache::getPlayers($params['room_id']);
        $foldPlayers = GameRoomCache::getFoldPlayers($params['room_id']);
        $playerId = $params['user_id'];
        $currentIndex = 0;
        if (is_array($players) && $players) {
            $currentIndex = array_search($playerId, $players);
        }

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

    public function getNextActionIndex($params)
    {
        $players = GameRoomCache::getPlayers($params['room_id']);
        $foldPlayers = GameRoomCache::getFoldPlayers($params['room_id']);
        $playerId = $params['user_id'];
        foreach ($players as $key => $player) {
            if ($playerId == $player) {
                $currentIndex = $key;
            }
        }

//        var_dump($currentIndex);die;

        $rotatePlayer = null;
        foreach ($players as $key => $player) {
            if (in_array($player, $foldPlayers)) {
                continue;
            }

            if (!$rotatePlayer) {
                $rotatePlayer = $player;
            }

            if (($currentIndex ?? 0) < $key) {
                return $player;
            }
        }

        return $rotatePlayer;
    }
    /**
     * gameCompleted
     *
     * @return GameService
     * @throws \Exception
     */
    public function gameCompleted($params): GameService
    {
        try {
            GameRoomCache::setWinnerCards($params['room_id'],null);
            GameRoomCache::setWinner($params['room_id'],null);
            GameRoomCache::setWinnerAmount($params['room_id'],null);
            GameRoomCache::setOtherPlayersStake($params['room_id'], null);
        } catch (\Exception $e) {
        }

        return $this;
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
            var_dump($e->getTraceAsString());die;
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
//            $previouslyBet = GameRoomCache::getPreviouslyBet($params['room_id']);
//            $bet = $previouslyBet * 2;
            $bet = $params['bet'];
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
            $bet = $previouslyBet;
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
            DB::beginTransaction();
            $account = Account::where('user_id', $params['user_id'])->first();
            if ($account->buy_in - $bet < 0) {
                return $this->left([
                    'player' => [
                        'id' => $params['user_id']
                    ],
                    'room_id' => $params['room_id']
                ]);
            }

            $account->decrement('balance', abs($bet));
            Account::where('user_id', $params['user_id'])->update([
                'buy_in' => DB::raw("buy_in - $bet")
            ]);

            $players = $this->getRoomPlayers($params);
            broadcast(new OnPlayersEvent($players->toJson(), $params['room_id']));

            AccountTransaction::create([
                'account_id' => $account->id,
                'amount' => -$bet,
                'balance' => $account->balance,
                'transactionable_type' => CasinoHoldem::class,
                'transactionable_id' => 1,
            ]);

            GameRoomCache::setActionIndex($params['room_id'], $this->getNextActionIndex($params));
            GameRoomCache::setBet($params['room_id'], $params['user_id'], $bet);
            GameRoomCache::setPot($params['room_id'], GameRoomCache::getPot($params['room_id']) + $bet);
            if ($bet) {
                GameRoomCache::setPreviouslyBet($params['room_id'], $bet);
            }

            $this->nextRound($params['room_id'], $params['user_id']);
            $this->sendNextPlayerActionMessage($params);
            $this->savePlayerChip($params['room_id'], $params['user_id'], $bet);
            DB::commit();
            broadcast(new GameRoomPlayEvent($params['room_id'], $params['user_id'], $bet));
        } catch (\Exception $e) {
            DB::rollBack();
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
        broadcast(new GameRoomStartEvent($params['room_id'], $this->getProvablyFairGame()));
        $players = $this->getRoomPlayers($params);
        broadcast(new OnPlayersEvent($players->toJson(), $params['room_id']));
        return $this;
    }

    public function left($params)
    {
        $player = $params['player'];
        if (!GameRoomCache::getPlayers($params['room_id'])) {
            return $this;
        }

        GameRoomPlayer::where('user_id', $player['id'])->delete();
        $players = $this->getRoomPlayers($params);
        if (GameRoomCache::getRound($params['room_id']) >= 1) {
            Account::where('user_id', $player['id'])->update([
                'balance' => DB::raw('balance + buy_in')
            ]);
            Account::where('user_id', $player['id'])->update([
                'buy_in' => 0,
            ]);
        }

        $params = [
            'room_id' => $params['room_id'],
            'user_id' => $player['id'],
        ];

        GameRoomCache::setActionIndex($params['room_id'], $this->getNextActionIndex($params));
        $endPlayer = GameRoomCache::getEndPlayer($params['room_id']);
        if ($params['user_id'] == $endPlayer) {
            GameRoomCache::setEndPlayer($params['room_id'], $this->changeEndPlayer($params));
        }

        $this->nextRound($params['room_id'], $params['user_id']);
        $this->setPlayerCanCheck($params['room_id']);
        GameRoomCache::removePlayer($params['room_id'], $player['id']);
        broadcast(new OnPlayersEvent($players->toJson(), $params['room_id'], $player['id']));
        if ($players->count() == 1) {
            $this->handleWinnerCards($params['room_id'], $players->first()->user_id);
            sleep(3);
            $this->moveToNextGame($params['room_id'], $players->first()->user_id);
            return $this;
        }

        broadcast(new GameRoomPlayEvent($params['room_id'], $params['user_id'], 0));
        $this->sendNextPlayerActionMessage($params);
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

    public function sendNextPlayerActionMessage($params)
    {
        $nextActionUserId = GameRoomCache::getActionIndex($params['room_id']);
        if ($nextActionUserId !== null && GameRoomCache::getRound($params['room_id']) <= 4) {
            $user = User::where('id', $nextActionUserId)->first() ?? null;
            if ($user) {
                $this->sendChatMessage($params['room_id'], $params['user_id'], "It is $user->name turn, you have 20 seconds to act");
            }
        }
    }

    public function getRoomPlayers($params)
    {
        $players = GameRoomPlayer::with([
            'user.account',
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
        $poker->addPlayers(count($players))->deal(2, 3)->play();
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

    private function resetPlayersCard($roomID, $players)
    {
        $provablyFairGame = $this->getProvablyFairGame();
        // load initially shuffled deck
        $deck = new CardDeck(explode(',', $provablyFairGame->secret));
        // cut the deck (provably fair)
        foreach ($players as $key => $player) {
            $deck->cut($provablyFairGame->shift_value % rand(1, 100));
            $poker = new Poker($deck);
            $poker->addPlayers(count($players))->deal(2, 3)->play();
            $cards = $poker->getPlayer($key+1)->getPocketCards()->map->code;
            GameRoomPlayerCard::updateOrCreate([
                'game_room_id' => $roomID,
                'user_id' => $player['id'],
            ],[
                'cards' => $cards
            ]);
        }
    }

    public function nextRound($roomId, $playerId)
    {
        $endPlayer = GameRoomCache::getEndPlayer($roomId);
        if ($playerId == $endPlayer) {
            $this->handleForNextRound($roomId, $playerId);
        }
    }

    public function handleWinnerCards($roomId, $winnerId = 0)
    {
        $playerIds = GameRoomCache::getPlayers($roomId);
        $playersCards = GameRoomPlayerCard::where('game_room_id', $roomId)->whereIn('user_id', $playerIds)->get();
        if (!$winnerId) {
            $winnerIndex = 0;
            $winner = new \App\Helpers\Games\PokerHand(collect($playersCards[$winnerIndex]->cards), collect(GameRoomCache::getCommunityCard($roomId)));
            for ($i = 1; $i < $playersCards->count(); $i++) {
                $competitor = new PokerHand(collect($playersCards[$i]->cards), collect(GameRoomCache::getCommunityCard($roomId)));
                if ($winner->compare($competitor) == -1) {
                    $winner = $competitor;
                    $winnerIndex = $i;
                }
            }

            $winnerCards = $playersCards[$winnerIndex]->cards;
            $winnerId =  $playersCards[$winnerIndex]->user_id;
        } else {
            $winnerCards = GameRoomPlayerCard::where('game_room_id', $roomId)->where('user_id', $winnerId)->first()->cards;
        }

        GameRoomCache::setWinner($roomId, $winnerId);
        GameRoomCache::setWinnerCards($roomId, $winnerCards);
        GameRoomCache::setPlayersCards($roomId, $playersCards->keyBy('user_id'));
        $user = User::where('id', $winnerId)->first();
        $pot = GameRoomCache::getPot($roomId);
        $this->sendChatMessage($roomId, $winnerId, "$user->name wins pot($pot) with high card king", "WinnerDeclare");

        // calculate winners amount and other players stake
        $winnersPercentage = 97.5; //todo- add this to env
        $winnersAmount = ($winnersPercentage/100) * $pot;
        Account::where('user_id', $winnerId)->update([
            'buy_in' => DB::raw("buy_in + $winnersAmount")
        ]);

        GameRoomCache::setWinnerAmount($roomId, $winnersAmount);
        //add winning amount to users account
        AccountService::updateUserOrAdminAccount($winnersAmount,$user->id);

        //add remainings 70% of 2.5% to admin
        $remainingPercentage = 2.5;
        $remainingTotalAmount = ($remainingPercentage/100)*$pot;
        $adminsStake = (70/100)*$remainingTotalAmount;
        AccountService::updateUserOrAdminAccount($adminsStake);

        // other players commission
        //commision logic - level 1 referer of this user will get 20%/number of players on table
        //  level 2 refrerr of this user will get 10%/number of players on table
        // if  any level is mising add that amount to admin account
        $players = User::whereIn('id', $playerIds)->get();
        $playersCount = $players->count();
        $commission = config('settings.affiliate.commissions.game_win.rates');
        $tierlyCommission[] =  (($commission[0]/100)*$remainingTotalAmount)/$playersCount;
        $tierlyCommission[] =  (($commission[1]/100)*$remainingTotalAmount)/$playersCount;
        GameRoomCache::setOtherPlayersStake($roomId, $tierlyCommission);
        // Log::debug("player to array",$players->toArray());
        foreach ($players as $key => $player) {
            event(new ResultEvent($player,$roomId));
        }
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
            $this->sendChatMessage($roomId, $playerId, 'Next to round '. GameRoomCache::getRound($roomId), 'NextGameRound');
        } else {
            $this->handleWinnerCards($roomId);
            sleep(3);
            $this->moveToNextGame($roomId, $playerId);
        }
    }

    private function moveToNextGame($roomId, $playerId)
    {
        // clear all current game
        $this->sendChatMessage($roomId, $playerId, 'Continue to start new game', 'StartGame');
        GameRoomCache::clearGameRoomCache($roomId);
        $players = $this->getRoomPlayers([
            'room_id' => $roomId
        ]);
        $playerIdsNextGame = [];
        foreach ($players as $player) {
            $playerIdsNextGame[]['id'] = $player->user_id;
        }

        $this->resetPlayersCard($roomId, $playerIdsNextGame);
        $players = $this->getRoomPlayers([
            'room_id' => $roomId
        ]);
        broadcast(new GameRoomStartEvent($roomId, $this->getProvablyFairGame()));
        broadcast(new OnPlayersEvent($players->toJson(), $roomId));
    }

    private function getNextPlayerCanCheck($params)
    {
        $players = GameRoomCache::getPlayers($params['room_id']);
        $foldPlayers = GameRoomCache::getFoldPlayers($params['room_id']);
        $playerId = $params['user_id'];
        if (is_array($players) && $players) {
            $currentIndex = array_search($playerId, $players);
        }

        $rotatePlayer = null;
        foreach ($players as $key => $player) {
            if (in_array($player, $foldPlayers)) {
                continue;
            }

            if (!$rotatePlayer) {
                $rotatePlayer = $player;
            }

            if (($currentIndex ?? 0) < $key) {
                return $player;
            }
        }

        return $rotatePlayer;
    }

    public function setPlayerCanCheck($roomId)
    {
        $players = GameRoomCache::getPlayers($roomId);
        GameRoomCache::setPlayerCanCheck($roomId, count($players) <= 3 ? reset($players) : $players[3]);
    }

    private function addCommunityCard()
    {
        $deck = new CardDeck(explode(',', $this->getProvablyFairGame()->secret));
        $deck->cut($this->getProvablyFairGame()->shift_value % 52);
        $poker = new Poker($deck);
        $poker->deal(2, 1)->play();

        return $poker->getCommunityCards()->map->code->first();
    }

    private function sendChatMessage($roomId, $userId, $message, $maintainMessage = '')
    {
        // store message
        $chatRoom = ChatRoom::where('room_id', $roomId)->first();
        $chatMessage = ChatMessage::create([
            'room_id' => $chatRoom->id,
            'user_id' => $userId,
            'message' => $message,
            'sys' => 1,
            'maintaingame' => $maintainMessage
        ]);

        broadcast(new ChatMessageSent($chatRoom, $chatMessage));
    }

    private function savePlayerChip($roomId, $userId, $chip)
    {
        GamePlayerChip::updateOrCreate([
            'game_room_id' => $roomId,
            'user_id' => $userId
        ], [
            'chip' => DB::raw("chip + $chip")
        ]);
    }
}
