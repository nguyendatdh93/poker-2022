<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameRoomCommunityCard extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'cards' => 'array'
    ];
}
