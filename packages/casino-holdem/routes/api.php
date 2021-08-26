<?php

use Packages\CasinoHoldem\Http\Controllers\GameController;

// game routes
Route::name('games.casino-holdem.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/casino-holdem/play', [GameController::class, 'play'])->name('play');
        Route::post('api/games/casino-holdem/fold', [GameController::class, 'fold'])->name('fold');
        Route::post('api/games/casino-holdem/call', [GameController::class, 'call'])->name('call');
        Route::post('api/games/casino-holdem/bet', [GameController::class, 'bet'])->name('bet');
        Route::post('api/games/casino-holdem/raise', [GameController::class, 'raise'])->name('raise');
        Route::post('api/games/casino-holdem/check', [GameController::class, 'check'])->name('check');
        Route::post('api/games/casino-holdem/left', [GameController::class, 'left'])->name('left');
        Route::post('api/games/casino-holdem/players', [GameController::class, 'onPlayers'])->name('on.players');
        Route::post('api/games/casino-holdem/community-card', [GameController::class, 'showCommunityCard'])->name('community.card');
        Route::post('api/games/casino-holdem/action', [GameController::class, 'action'])->name('action');
        Route::post('api/games/casino-holdem/game-completed', [GameController::class, 'gameCompleted'])->name('game-completed');
    });
