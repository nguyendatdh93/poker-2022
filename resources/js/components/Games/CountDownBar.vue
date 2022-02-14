<template>
  <div class="progress_bar">
    <countdown v-if="opponent.user_id == gameRoom.action_index && gameRoom.round <= 4 && start" :left-time="3000000"
               @start="startCountDown"
               @finish="finishCountdown">
      <template slot="process" slot-scope="{ timeObj }">
        <v-progress-linear
            class="progress-bar"
            color="light-blue"
            height="10"
            buffer-value="100"
            :value="timeObj.ceil.s * (100/30)"
            striped
        ></v-progress-linear>
      </template>
    </countdown>
  </div>
</template>

<script>
import axios from "axios";
import {mapState} from "vuex";

export default {
  name: "CountDownBar",
  props: ['opponent', 'start', 'provably-fair-game', 'room', 'user'],
  computed: {
    ...mapState('game-room', ['players', 'gameRoom']),
  },
  methods: {
    async finishCountdown() {
      this.$emit('finish', false);
      await axios.post('/api/games/casino-holdem/fold', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
        user_id: this.gameRoom.action_index,
        user_action_index: this.getPlayerActionIndex(this.user.id)
      });
    },
    startCountDown() {
      this.$emit('start', true);
    },
    getPlayerActionIndex(playerId) {
      if (this.gameRoom.players) {
        let players = Object.values(this.gameRoom.players);
        return players.findIndex((player) => {
          return player == playerId;
        })
      }

      return -1;
    },
  }
}
</script>

<style scoped>

</style>