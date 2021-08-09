<?php

namespace App\Http\Controllers;

use App\Helpers\Games\CardDeck;
use App\Helpers\Games\Poker;
use App\Helpers\PackageManager;
use App\Http\Requests\CreateGameRoom;
use App\Http\Requests\GetGameRooms;
use App\Http\Requests\JoinGameRoom;
use App\Http\Requests\LeaveGameRoom;
use App\Models\ChatRoom;
use App\Models\GameRoom;
use App\Models\GameRoomPlayer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Packages\CasinoHoldem\Models\CasinoHoldem;

class GameRoomController extends Controller
{
    public function index(GetGameRooms $request, $packageId, PackageManager $packageManager)
    {
        // get all open rooms and players
        $rooms = GameRoom::where('gameable_type', $packageManager->get($packageId)->model)
            ->open()
            ->with('players')
            ->withCount('players')
            ->orderBy('id', 'desc')
            ->get();

        // find a room which current user has joined
        $room = $rooms
            ->filter(function ($room) use ($request) {
                return $room->player($request->user());
            })
            ->first();

        $gameable = new CasinoHoldem();
        if ($room->id ?? false) {
            $deck = new CardDeck();
            $poker = new Poker($deck);
            $poker->addPlayers(2)->deal(2, 3)->play();
            $gameable->player_cards = $poker->getPlayer(1)->getPocketCards()->map->code;
            $gameable->community_cards = $poker->getCommunityCards()->map->code;
        }

        // find the game model for the given room and user
        $game = $room
            ? $room->player($request->user())->game
            : NULL;

        if ($room && $gameable ?? false) {
            $room->gameable = $gameable;
        }

        return $room
            ? [
                'room' => $room,
                'game' => $game ? $game->loadMissing('gameable') : NULL
            ]
            : [
                // filter out rooms that are already full
                'rooms' => $rooms
                    ->filter(function ($room) {
                        return $room->players_count < $room->parameters->players_count;
                    })
                    ->values()
                    ->map
                    ->only(['id', 'name', 'parameters', 'players_count'])
            ];
    }

    /**
     * Create game room and join it automatically
     *
     * @param Request $request
     * @param $packageId
     * @param PackageManager $packageManager
     * @return array
     */
    public function create(CreateGameRoom $request, $packageId, PackageManager $packageManager)
    {
        try {
            DB::beginTransaction();
            $parameters = ['stakes'=>$request->stakes,
            'buy_in'=>$request->buy_in,
            'games_type'=>$request->games_type,
            'players_count'=>$request->players_count,
            'bet'=>$request->bet
        ];
       
            $room = new GameRoom();
            $room->owner()->associate($request->user());
            $room->name = $request->name;
            $room->gameable_type = $packageManager->get($packageId)->model;
            $room->status = GameRoom::STATUS_OPEN;
            $room->parameters = $request->parameters??$parameters;
            $room->save();

            // create new chat room
            $chatRoom = new ChatRoom();
            $chatRoom->name = $request->name;
            $chatRoom->room_id = $room->id;
            $chatRoom->save();

            DB::commit();
            return $this->joinGameRoom($room, $request->user());
        } catch (\Exception $e) {
            DB::rollBack();
            var_dump($e->getMessage());
        }
    }

    /**
     * Join existing game room
     *
     * @param Request $request
     * @param GameRoom $gameRoom
     * @return array
     */
    public function join(JoinGameRoom $request, $packageId)
    {
        $room = GameRoom::find($request->room_id);
        $gameable = new CasinoHoldem();
        if ($room->id ?? false) {
            $players = GameRoomPlayer::where('game_room_id', $room->id)->orderBy('created_at', 'asc')->get();
            if ($players->count() > 2) {
                $room->dealer =$players->first();
                $room->small_blind = $players->skip(1)->take(1)->first();
                $room->big_blind = $players->skip(2)->take(1)->first();
            } else {
                $room->small_blind = $players->first();
                $room->big_blind = $players->skip(1)->take(1)->first();
            }

            // load initially shuffled deck
            $deck = new CardDeck();
            $poker = new Poker($deck);
            $poker->addPlayers(2)->deal(2, 3)->play();
            $gameable->player_cards = $poker->getPlayer(1)->getPocketCards()->map->code;
            $gameable->community_cards = $poker->getCommunityCards()->map->code;
            $gameable->player_hand = $poker->getPlayer(1)->getHand()->get()->map->code;
            $gameable->player_hand_rank = $poker->getPlayer(1)->getHand()->getRank();
        }

        if ($room && $gameable ?? false) {
            $room->gameable = $gameable;
        }

        return $this->joinGameRoom($room, $request->user());
    }

    /**
     * Leave existing game room
     *
     * @param JoinGameRoom $request
     * @param $packageId
     * @return array
     */
    public function leave(LeaveGameRoom $request, $packageId)
    {
        GameRoomPlayer::where('game_room_id', $request->room_id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return $this->successResponse();
    }

    private function joinGameRoom(GameRoom $room, User $user)
    {
        $player = new GameRoomPlayer();
        $player->room()->associate($room);
        $player->user()->associate($user);
        $player->save();

        return $this->successResponse(['room' => $room,'message'=>'New room created successfully']);
    }
}
