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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $gameRoom = GameRoom::where('id', $params['room_id'])->first();
        $previouslyBet = GameRoomCache::getPreviouslyBet($params['room_id']);
        GameRoomCache::setActionIndex($params['room_id'], $params['user_action_index'] == $gameRoom->parameters->players_count - 1 ? 0 : $params['user_action_index'] + 1);
        $this->nextRound($params['room_id'], $params['user_id']);
        GameRoomCache::setFoldPlayer($params['room_id'], $params['user_id']);
        broadcast(new GameRoomPlayEvent($params['room_id'], $previouslyBet));
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
            $account = Account::where('user_id', $params['user_id'])->first();
            $gameRoom = GameRoom::where('id', $params['room_id'])->first();
            $previouslyBet = GameRoomCache::getPreviouslyBet($params['room_id']);
            $account->decrement('balance', abs($previouslyBet));

            AccountTransaction::create([
                'account_id' => $account->id,
                'amount' => -$previouslyBet,
                'balance' => $account->balance,
                'transactionable_type' => CasinoHoldem::class,
                'transactionable_id' => 1,
            ]);

            GameRoomCache::setActionIndex($params['room_id'], $params['user_action_index'] == $gameRoom->parameters->players_count - 1 ? 0 : $params['user_action_index'] + 1);
            $this->nextRound($params['room_id'], $params['user_id']);
            GameRoomCache::setBet($params['room_id'], $params['user_id'], $previouslyBet);
            broadcast(new GameRoomPlayEvent($params['room_id'], $previouslyBet));
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
            $account = Account::where('user_id', $params['user_id'])->first();
            $gameRoom = GameRoom::where('id', $params['room_id'])->first();
            $previouslyBet = GameRoomCache::getPreviouslyBet($params['room_id']);
            $bet = $previouslyBet * 2;
            $account->decrement('balance', abs($bet));

            AccountTransaction::create([
                'account_id' => $account->id,
                'amount' => -$bet,
                'balance' => $account->balance,
                'transactionable_type' => CasinoHoldem::class,
                'transactionable_id' => 1,
            ]);

            GameRoomPlayerBet::updateOrCreate([
                'game_room_id' => $params['room_id'],
                'user_id' => $params['user_id'],
            ], [
                'bet' => $bet,
                'round' => $gameRoom->round,
            ]);

            GameRoomCache::setActionIndex($params['room_id'], $params['user_action_index'] == $gameRoom->parameters->players_count - 1 ? 0 : $params['user_action_index'] + 1);
            $this->nextRound($params['room_id'], $params['user_id']);
            GameRoomCache::setBet($params['room_id'], $params['user_id'], $bet);
            GameRoomCache::setPreviouslyBet($params['room_id'], $bet);
            broadcast(new GameRoomPlayEvent($params['room_id'], $bet));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this;
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

    private function getRoomPlayers($params)
    {
        $players = GameRoomPlayer::with([
            'gameRoomPlayerCards' => function($query) use ($params) {
                return $query->where('game_room_id', $params['room_id']);
            }
        ])->where('game_room_id', $params['room_id'])->orderBy('id', 'asc')->get();
        foreach ($players as $key => $player) {
            $players[$key]['cards'] = $player->gameRoomPlayerCards->first()->cards;
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
        $round = GameRoomCache::getRound($roomId);
        if ($round == 1 || $round == 2) {
            if (in_array($playerId, [GameRoomCache::getBigBlind($roomId)])) {
                GameRoomCache::setRound($roomId, GameRoomCache::getRound($roomId) + 1);
            }
        } else {
            if (in_array($playerId, [GameRoomCache::getSmallBlind($roomId), GameRoomCache::getDealer($roomId)])) {
                GameRoomCache::setRound($roomId, GameRoomCache::getRound($roomId) + 1);
            }
        }

    }
}
