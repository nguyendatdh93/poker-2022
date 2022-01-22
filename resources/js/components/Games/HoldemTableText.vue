<template>
  <div class="table-text">
    <div class="table-text1">
      <div id="table-text">
        <div class="pot" v-if="gameRoom.pot > 0">
          <span>Pot: {{ gameRoom.pot }}</span>
        </div>
        <div class="winning-score" v-if="gameDisplayPot > 0"><img src="/v2/images/pokr-icon.png" alt="" /> <span class="w_points">{{ gameDisplayPot }}</span></div>
        <input type="hidden" :value="getDisplayPot()">
        <div class="righttext">
          <ul>
            <li>Real Money Table</li>
            <li>Bussolini III - No Limit Hold'em</li>
            <li>50/100 Play Money</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "HoldemTableText",
   data() {
    return {
      gameDisplayPot: 0,
    }
  },
  computed: {
    ...mapState('game-room', ['players','gameRoom','collectpot','roomPot']),
  },
   methods: {
    getDisplayPot(){
      if(this.collectpot == 1)
      {
        let potIndex = this.roomPot.findIndex(rooms => rooms.roomid == this.players[0].game_room_id);
        if(potIndex > -1)
        {
            this.gameDisplayPot = parseInt(this.roomPot[potIndex].collectpot);
        }
        this.$store.commit('game-room/GAME_ROOM_COLLECT_POTS', 2);
      }
      else if (this.collectpot == 0) {
        this.gameDisplayPot = 0;
      }
      return this.gameDisplayPot;
    }
   }
}
</script>

<style scoped>
#table-text {
  bottom: unset !important;
}
</style>