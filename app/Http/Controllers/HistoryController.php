<?php

namespace App\Http\Controllers;

use App\Helpers\PackageManager;
use App\Helpers\Queries\GameHistoryQuery;
use App\Helpers\Queries\GameLossQuery;
use App\Helpers\Queries\GameWinQuery;
use App\Http\Requests\GetGame;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HistoryController extends Controller
{
    public function user(Request $request, GameHistoryQuery $query)
    {
        $account = $request->user()->account;

        $items = $query
            ->addWhere(['account_id', '=', $account->id])
            ->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function recent(GameHistoryQuery $query)
    {
        $items = $query
            ->addWhere(['created_at', '>', Carbon::now()->subDays(3)])
            ->getBuilder()
            // Load relations with specific columns (such as name) that can be safely shared with all users.
            ->with('account:id,user_id', 'account.user:id,name,email,avatar,last_seen_at') // email is required for avatar_url to work
            ->get()
            ->map(function (Game $game) {
                $game->account->user->makeHidden(['email']);
                return $game;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function wins(GameWinQuery $query)
    {
        $items = $query
            ->get()
            ->map(function (Game $game) {
                $game->account->user->makeHidden(['email']);
                return $game;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function losses(GameLossQuery $query)
    {
        $items = $query
            ->get()
            ->map(function (Game $game) {
                $game->account->user->makeHidden(['email']);
                return $game;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(GetGame $request, Game $game)
    {
        // make all gameable hidden attributes visible
        $game->loadMissing('account:id,user_id', 'account.user:id,name', 'gameable');
        $game->gameable->makeVisible($game->gameable->getHidden());

        return ['game' => $game];
    }

    public function package(GetGame $request, Game $game, PackageManager $packageManager)
    {
        return ['id' => $packageManager->getPackageIdByClass($game->gameable_type)];
    }

    public function verify(GetGame $request, Game $game)
    {
        // make all gameable hidden attributes visible
        $game->gameable->makeVisible($game->gameable->getHidden())->append('secret_attribute', 'secret_attribute_hint');
        $game->provablyFairGame->makeVisible(['secret', 'server_seed'])->append('client_hash', 'shift_value');

        return ['game' => $game];
    }
}
