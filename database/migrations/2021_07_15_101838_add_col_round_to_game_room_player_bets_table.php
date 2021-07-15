<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColRoundToGameRoomPlayerBetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_room_player_bets', function (Blueprint $table) {
            $table->integer('round')->after('bet')->default(1)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_room_player_bets', function (Blueprint $table) {
            $table->dropColumn('round');
        });
    }
}
