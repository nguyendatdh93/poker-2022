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
        return Cache::tags(["room:$roomId"])->get("big:blind:$roomId");
    }

    /**
     * @param mixed $bigBlind
     */
    public static function setBigBlind($roomId, $bigBlindId): void
    {
        Cache::tags(["room:$roomId"])->put("big:blind:$roomId", $bigBlindId);
    }

    /**
     * @return mixed
     */
    public static function getSmallBlind($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("small:blind:$roomId");
    }

    /**
     * @param mixed $smallBlind
     */
    public static function setSmallBlind($roomId, $smallBlindId): void
    {
        Cache::tags(["room:$roomId"])->put("small:blind:$roomId", $smallBlindId);
    }

    /**
     * @return mixed
     */
    public static function getDealer($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("dealer:$roomId");
    }

    /**
     * @param mixed $dealer
     */
    public static function setDealer($roomId, $dealerId): void
    {
        Cache::tags(["room:$roomId"])->put("dealer:$roomId", $dealerId);
    }

    /**
     * @param mixed $dealer
     */
    public static function setRound($roomId, $round): void
    {
        Cache::tags(["room:$roomId"])->put("round:$roomId", $round);
    }

    /**
     * @param mixed $round
     */
    public static function getRound($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("round:$roomId");
    }

    /**
     * @param mixed $dealer
     */
    public static function setBet($roomId, $playerId, $bet): void
    {
        Cache::tags(["room:$roomId"])->put("bet:$roomId:player:$playerId", $bet);
    }

    public static function clearBet($roomId, $playerId)
    {
        Cache::tags(["room:$roomId"])->forget("bet:$roomId:player:$playerId");
    }

    /**
     * @param mixed $round
     */
    public static function getBets($roomId)
    {
        $playerIds = self::getPlayers($roomId);
        $bets = [];
        foreach ($playerIds ?? [] as $playerId) {
            $bets[$playerId] = Cache::tags(["room:$roomId"])->get("bet:$roomId:player:$playerId");
        }

        return $bets;
    }

    /**
     * @param mixed $dealer
     */
    public static function setPlayers($roomId, $playerIds): void
    {
        Cache::tags(["room:$roomId"])->put("players:$roomId", $playerIds);
    }

    /**
     * @param mixed $round
     */
    public static function getPlayers($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("players:$roomId");
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

    /**
     * @param mixed $dealer
     */
    public static function setActionIndex($roomId, $index): void
    {
        Cache::tags(["room:$roomId"])->put("action:index:$roomId", $index);
    }

    /**
     * @param mixed $round
     */
    public static function getActionIndex($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("action:index:$roomId");
    }

    /**
     * @param mixed $dealer
     */
    public static function setPot($roomId, $pot): void
    {
        Cache::tags(["room:$roomId"])->put("pot:$roomId", $pot);
    }

    /**
     * @param mixed $round
     */
    public static function getPot($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("pot:$roomId");
    }

    /**
     * @param mixed
     */
    public static function setCommunityCard($roomId, $cards)
    {
        Cache::tags(["room:$roomId"])->put("community:card:$roomId", $cards);
    }

    /**
     * @param mixed
     */
    public static function setWinner($roomId, $playerId)
    {
        Cache::tags(["room:$roomId"])->put("winner:$roomId", $playerId);
    }

    /**
     * @param mixed $round
     */
    public static function getWinner($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("winner:$roomId");
    }

    /**
     * @param mixed
     */
    public static function setWinnerCards($roomId, $cards)
    {
        Cache::tags(["room:$roomId"])->put("winner:cards:$roomId", $cards);
    }

    /**
     * @param mixed $round
     */
    public static function getWinnerCards($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("winner:cards:$roomId");
    }

   /**
     * @param mixed
     */
    public static function setWinnerAmount($roomId, $amount)
    {
        Cache::tags(["room:$roomId"])->put("winner:amount:$roomId", $amount);
    }

    /**
     * @param mixed $round
     */
    public static function getWinnerAmount($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("winner:amount:$roomId");
    }
    /**
     * @param mixed
     */
    public static function setPlayersCards($roomId, $cards)
    {
        Cache::tags(["room:$roomId"])->put("players:cards:$roomId", $cards);
    }

    /**
     * @param mixed $round
     */
    public static function getPlayersCards($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("players:cards:$roomId");
    }


    /**
     * @param mixed
     */
    public static function setPreviouslyBet($roomId, $bet)
    {
        Cache::tags(["room:$roomId"])->put("previously:bet:$roomId", $bet);
    }

    /**
     * @param mixed $round
     */
    public static function getPreviouslyBet($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("previously:bet:$roomId");
    }

    /**
     * @param mixed $round
     */
    public static function getCommunityCard($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("community:card:$roomId");
    }

    /**
     * @param mixed
     */
    public static function setFoldPlayer($roomId, $playerId)
    {
        Cache::tags(["room:$roomId", "fold:$roomId:$playerId"])->put("fold:$roomId:player:$playerId", $playerId);
    }

    public static function getFoldPlayer($roomId, $playerId)
    {
        return Cache::tags(["room:$roomId", "fold:$roomId:$playerId"])->get("fold:$roomId:player:$playerId");
    }

    public static function clearFoldPlayer($roomId, $playerId)
    {
        Cache::tags(["room:$roomId", "fold:$roomId:$playerId"])->forget("fold:$roomId:player:$playerId");
    }

    public static function clearFoldPlayers($roomId)
    {
        $playerIds = GameRoomPlayer::where('game_room_id', $roomId)->get()->pluck('user_id');
        foreach ($playerIds ?? [] as $playerId) {
            Cache::tags("fold:$roomId:$playerId")->flush();
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
            if ($player = Cache::tags(["room:$roomId", "fold:$roomId:$playerId"])->get("fold:$roomId:player:$playerId")) {
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
        return Cache::tags(["room:$roomId"])->get("player:can:check:$roomId");
    }

    /**
     * @param $roomId
     * @param $index
     */
    public static function setPlayerCanCheck($roomId, $index)
    {
        Cache::tags(["room:$roomId"])->put("player:can:check:$roomId", $index);
    }

    /**
     * @param $roomId
     * @return mixed
     */
    public static function getEndPlayer($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("end:player:$roomId");
    }

    /**
     * @param $roomId
     * @param $index
     */
    public static function setEndPlayer($roomId, $playerId)
    {
        Cache::tags(["room:$roomId"])->put("end:player:$roomId", $playerId);
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
        Cache::tags(["room:$roomId"])->put("other_players_stake:$roomId", $amount);
    }

    /**
     * @param mixed $round
     */
    public static function getOtherPlayersStake($roomId)
    {
        return Cache::tags(["room:$roomId"])->get("other_players_stake:$roomId");
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
        Cache::tags(["room:$roomId"])->flush();
    }
}