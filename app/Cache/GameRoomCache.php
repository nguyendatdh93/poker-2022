<?php


namespace App\Cache;


use App\Models\GameRoomPlayer;
use Illuminate\Support\Facades\Cache;

class GameRoomCache
{
    /**
     * @return mixed
     */
    public static function getBigBlind($roomId)
    {
        return Cache::get("big:blind:$roomId");
    }

    /**
     * @param mixed $bigBlind
     */
    public static function setBigBlind($roomId, $bigBlindId): void
    {
        Cache::put("big:blind:$roomId", $bigBlindId);
    }

    /**
     * @return mixed
     */
    public static function getSmallBlind($roomId)
    {
        return Cache::get("small:blind:$roomId");
    }

    /**
     * @param mixed $smallBlind
     */
    public static function setSmallBlind($roomId, $smallBlindId): void
    {
        Cache::put("small:blind:$roomId", $smallBlindId);
    }

    /**
     * @return mixed
     */
    public static function getDealer($roomId)
    {
        return Cache::get("dealer:$roomId");
    }

    /**
     * @param mixed $dealer
     */
    public static function setDealer($roomId, $dealerId): void
    {
        Cache::put("dealer:$roomId", $dealerId);
    }

    /**
     * @param mixed $dealer
     */
    public static function setRound($roomId, $round): void
    {
        Cache::put("round:$roomId", $round);
    }

    /**
     * @param mixed $round
     */
    public static function getRound($roomId)
    {
        return Cache::get("round:$roomId");
    }

    /**
     * @param mixed $dealer
     */
    public static function setBet($roomId, $playerId, $bet): void
    {
        Cache::put("bet:$roomId:player:$playerId", $bet);
    }

    public static function clearBet($roomId, $playerId)
    {
        Cache::forget("bet:$roomId:player:$playerId");
    }

    /**
     * @param mixed $round
     */
    public static function getBets($roomId)
    {
        $playerIds = self::getPlayers($roomId);
        $bets = [];
        foreach ($playerIds ?? [] as $playerId) {
            $bets[$playerId] = Cache::get("bet:$roomId:player:$playerId");
        }

        return $bets;
    }

    /**
     * @param mixed $dealer
     */
    public static function setPlayers($roomId, $playerIds): void
    {
        Cache::put("players:$roomId", $playerIds);
    }

    /**
     * @param mixed $round
     */
    public static function getPlayers($roomId)
    {
        return Cache::get("players:$roomId");
    }

    public static function removePlayer($roomId, $playerId)
    {
        $playerIds = self::getPlayers($roomId);
        foreach ($playerIds ?? [] as $key => $id) {
            if ($id == $playerId) {
                unset($playerIds[$key]);
            }
        }

        self::setPlayers($roomId, array_values($playerIds));
    }

    public static function clearPlayers($roomId)
    {
        self::setEndPlayer($roomId, null);
    }

    /**
     * @param mixed $dealer
     */
    public static function setActionIndex($roomId, $index): void
    {
        Cache::put("action:index:$roomId", $index);
    }

    /**
     * @param mixed $round
     */
    public static function getActionIndex($roomId)
    {
        return Cache::get("action:index:$roomId");
    }

    /**
     * @param mixed $dealer
     */
    public static function setPot($roomId, $pot): void
    {
        Cache::put("pot:$roomId", $pot);
    }

    /**
     * @param mixed $round
     */
    public static function getPot($roomId)
    {
        return Cache::get("pot:$roomId");
    }

    /**
     * @param mixed
     */
    public static function setCommunityCard($roomId, $cards)
    {
        Cache::put("community:card:$roomId", $cards);
    }

    /**
     * @param mixed
     */
    public static function setWinner($roomId, $playerId)
    {
        Cache::put("winner:$roomId", $playerId);
    }

    /**
     * @param mixed $round
     */
    public static function getWinner($roomId)
    {
        return Cache::get("winner:$roomId");
    }

    /**
     * @param mixed
     */
    public static function setWinnerCards($roomId, $cards)
    {
        Cache::put("winner:cards:$roomId", $cards);
    }

    /**
     * @param mixed $round
     */
    public static function getWinnerCards($roomId)
    {
        return Cache::get("winner:cards:$roomId");
    }

   /**
     * @param mixed
     */
    public static function setWinnerAmount($roomId, $amount)
    {
        Cache::put("winner:amount:$roomId", $amount);
    }

    /**
     * @param mixed $round
     */
    public static function getWinnerAmount($roomId)
    {
        return Cache::get("winner:amount:$roomId");
    }
    /**
     * @param mixed
     */
    public static function setPlayersCards($roomId, $cards)
    {
        Cache::put("players:cards:$roomId", $cards);
    }

    /**
     * @param mixed $round
     */
    public static function getPlayersCards($roomId)
    {
        return Cache::get("players:cards:$roomId");
    }


    /**
     * @param mixed
     */
    public static function setPreviouslyBet($roomId, $bet)
    {
        Cache::put("previously:bet:$roomId", $bet);
    }

    /**
     * @param mixed $round
     */
    public static function getPreviouslyBet($roomId)
    {
        return Cache::get("previously:bet:$roomId");
    }

    /**
     * @param mixed $round
     */
    public static function getCommunityCard($roomId)
    {
        return Cache::get("community:card:$roomId");
    }

    /**
     * @param mixed
     */
    public static function setFoldPlayer($roomId, $playerId)
    {
        Cache::put("fold:$roomId:player:$playerId", $playerId);
    }

    public static function getFoldPlayer($roomId, $playerId)
    {
        return Cache::get("fold:$roomId:player:$playerId");
    }

    public static function clearFoldPlayer($roomId, $playerId)
    {
        Cache::forget("fold:$roomId:player:$playerId");
    }

    public static function clearFoldPlayers($roomId)
    {
        $playerIds = GameRoomPlayer::where('game_room_id', $roomId)->get()->pluck('user_id');
        $players = [];
        foreach ($playerIds ?? [] as $playerId) {
            Cache::forget("fold:$roomId:player:$playerId");
        }
    }

    /**
     * @param mixed $round
     */
    public static function getFoldPlayers($roomId)
    {
        $playerIds = GameRoomPlayer::where('game_room_id', $roomId)->get()->pluck('user_id');
        $players = [];
        foreach ($playerIds ?? [] as $playerId) {
            if ($player = Cache::get("fold:$roomId:player:$playerId")) {
                $players[$playerId] = $player;
            }
        }

        return $players;
    }

    /**
     * @param $roomId
     * @return mixed
     */
    public static function getPlayerCanCheck($roomId)
    {
        return Cache::get("player:can:check:$roomId");
    }

    /**
     * @param $roomId
     * @param $index
     */
    public static function setPlayerCanCheck($roomId, $index)
    {
        Cache::put("player:can:check:$roomId", $index);
    }

    /**
     * @param $roomId
     * @return mixed
     */
    public static function getEndPlayer($roomId)
    {
        return Cache::get("end:player:$roomId");
    }

    /**
     * @param $roomId
     * @param $index
     */
    public static function setEndPlayer($roomId, $playerId)
    {
        Cache::put("end:player:$roomId", $playerId);
    }

    /**
     * @param $roomId
     * @return mixed
     */
    public static function getPreviousSmallBlindIndex($roomId)
    {
        return Cache::get("previous:small:blind:$roomId");
    }

    /**
     * @param $roomId
     * @param $index
     */
    public static function setPreviousSmallBlindIndex($roomId, $index)
    {
        Cache::put("previous:small:blind:$roomId", $index);
    }

    /**
     * @param mixed
     */
    public static function setOtherPlayersStake($roomId, $amount)
    {
        Cache::put("other_players_stake:$roomId", $amount);
    }

    /**
     * @param mixed $round
     */
    public static function getOtherPlayersStake($roomId)
    {
        return Cache::get("other_players_stake:$roomId");
    }

    public static function getGameRoomCache($roomId)
    {
        return [
            'players' => self::getPlayers($roomId),
            'big_blind' => self::getBigBlind($roomId),
            'small_blind' => self::getSmallBlind($roomId),
            'dealer' => self::getDealer($roomId),
            'round' => self::getRound($roomId),
            'bets' => self::getBets($roomId),
            'community_card' => self::getCommunityCard($roomId),
            'action_index' => self::getActionIndex($roomId),
            'previously_bet' => self::getPreviouslyBet($roomId),
            'fold_players' => self::getFoldPlayers($roomId),
            'pot' => self::getPot($roomId),
            'winner' => self::getWinner($roomId),
            'winner_cards' => self::getWinnerCards($roomId),
            'winner_amount' => self::getWinnerAmount($roomId),
            'player_cards' => self::getPlayersCards($roomId),
            'player_can_check' => self::getPlayerCanCheck($roomId),
            'end_player' => self::getEndPlayer($roomId),
        ];
    }

    public static function clearGameRoomCache($roomId)
    {
        self::clearPlayers($roomId);
        self::setBigBlind($roomId, null);
        self::setSmallBlind($roomId, null);
        self::setDealer($roomId, null);
        self::setRound($roomId, null);
        self::setCommunityCard($roomId, null);
        self::setActionIndex($roomId, null);
        self::setPreviouslyBet($roomId, null);
        self::setPlayersCards($roomId, null);
        self::setWinnerCards($roomId, null);
        self::setEndPlayer($roomId, null);
        self::setWinnerAmount($roomId, null);
        self::clearFoldPlayers($roomId);
        self::setOtherPlayersStake($roomId, null);

    }
}