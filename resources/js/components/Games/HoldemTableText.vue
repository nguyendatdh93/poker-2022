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
      gameDisplayPot: 5,
    }
  },
  computed: {
    ...mapState('game-room', ['gameRoom','collectpot']),
  },
   methods: {
    getDisplayPot(){
      if(this.collectpot == 1)
      {
        this.$store.commit('game-room/GAME_ROOM_COLLECT_POTS', 2);
        this.gameDisplayPot = parseInt(this.gameRoom.pot);
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