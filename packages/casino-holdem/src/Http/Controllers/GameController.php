<?php

namespace Packages\CasinoHoldem\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GameRoom;
use Packages\CasinoHoldem\Http\Requests\Play;
use Packages\CasinoHoldem\Http\Requests\Fold;
use Packages\CasinoHoldem\Http\Requests\Call;
use Packages\CasinoHoldem\Services\GameService;

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
            ->fold($request->only(['bet', 'bonus_bet', 'room_id', 'user_id']))
            ->getGame();
    }

    public function call(Call $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->call()
            ->getGame();
    }
}
