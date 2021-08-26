<?php

namespace Packages\CasinoHoldem\Http\Controllers;

use App\Events\OnPlayersEvent;
use App\Http\Controllers\Controller;
use App\Models\GameRoom;
use App\Models\GameRoomPlayer;
use Packages\CasinoHoldem\Http\Requests\Action;
use Packages\CasinoHoldem\Http\Requests\Play;
use Packages\CasinoHoldem\Http\Requests\Fold;
use Packages\CasinoHoldem\Http\Requests\Call;
use Packages\CasinoHoldem\Http\Requests\Raise;
use Packages\CasinoHoldem\Services\GameService;
use Illuminate\Http\Request;
class GameController extends Controller
{
    public function play(Play $request, GameRoom $room, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->deal($request->only(['bet', 'bonus_bet', 'is_big_blind', 'is_small_blind']))
            ->getGame();
    }

    public function fold(Fold $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->fold($request->only(['room_id', 'user_id', 'user_action_index']))
            ->getGame();
    }

    public function call(Call $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->call($request->only(['bet', 'room_id', 'user_id', 'user_action_index']))
            ->getGame();
    }

    public function bet(Raise $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->bet($request->only(['bet', 'room_id', 'user_id', 'user_action_index']))
            ->getGame();
    }

    public function raise(Raise $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->raise($request->only(['bet', 'room_id', 'user_id', 'user_action_index']))
            ->getGame();
    }

    public function check(Raise $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->check($request->only(['bet', 'room_id', 'user_id', 'user_action_index']))
            ->getGame();
    }

    public function onPlayers(Action $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->onPlayers($request->only(['room_id', 'players']))
            ->getGame();
    }

    public function left(Action $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->left($request->only(['room_id', 'player']))
            ->getGame();
    }

    public function showCommunityCard(Action $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->communityCard($request->only(['room_id']))
            ->getGame();
    }

    public function action(Action $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->action($request->only(['room_id']))
            ->getGame();
    }
    public function gameCompleted(Request $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->gameCompleted($request->only(['room_id']))
            ->getGame();
    }
}
