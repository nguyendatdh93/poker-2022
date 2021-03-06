<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRoomPlayer extends Model
{
    protected $guarded = [];
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['room'];

    public function room()
    {
        return $this->belongsTo(GameRoom::class, 'game_room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function gameRoomPlayerCards()
    {
        return $this->hasMany(GameRoomPlayerCard::class, 'user_id','user_id');
    }
}
