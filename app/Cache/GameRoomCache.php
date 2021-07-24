<?php


namespace App\Cache;


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

        self::setPlayers($roomId, $playerIds);
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
     * @param mixed
     */
    public static function setCommunityCard($roomId, $cards)
    {
        Cache::put("community:card:$roomId", $cards);
    }

    /**
     * @param mixed $round
     */
    public static function getPreviouslyBet($roomId)
    {
        return Cache::get("previously:bet:$roomId");
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

    /**
     * @param mixed $round
     */
    public static function getFoldPlayers($roomId)
    {
        $playerIds = self::getPlayers($roomId);
        $players = [];
        foreach ($playerIds ?? [] as $playerId) {
            if ($player = Cache::get("fold:$roomId:player:$playerId")) {
                $players[$playerId] = $player;
            }
        }

        return $players;
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
        ];
    }
}