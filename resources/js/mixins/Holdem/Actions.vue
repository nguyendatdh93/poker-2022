<template>
  <div class="d5-flex justify-center flex-wrap mt-10" id="player_actions">
    <v-btn
        :disabled="!provablyFairGame.hash || foldPlayers[user.id]"
        class="mx-1 my-2 my-lg-0"
        small
        @click="onFold()"
    > Fold
    </v-btn>
    <v-btn
        :disabled="!provablyFairGame.hash || foldPlayers[user.id]"
        class="mx-1 my-2 my-lg-0"
        small
        @click="doCall({
              room_id: room.id,
              user_id: user.id,
            })"
    > Call
    </v-btn>
  </div>
</template>

<script>
import axios from "axios";
import {mapState} from "vuex";

export default {
  name: "Actions",
  props: ['room', 'provably-fair-game', 'user'],
  computed: {
    ...mapState('broadcasting', ['echo']),
    ...mapState('game-room', ['foldPlayers']),
  },
  created() {
    this.echo.join(`game.${this.room.id}`)
        .listen('FoldEvent', data => {
          this.$store.dispatch('game-room/setFoldPlayers', data.players)
        });
  },
  methods: {
    async onFold() {
      await axios.post('/api/games/casino-holdem/fold', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
        user_id: this.user.id
      });
    },
    doCall(params) {
      // let index = this.players.findIndex(player => player.id = this.user.id);
      const endpoint = this.getRoute('call')
      const {data: game} = axios.post(endpoint, {
        hash: this.provablyFairGame.hash,
        user_id : this.user.id,
        room_id: params.room_id,
        bet: this.room.parameters.bet * 2,
      });

      this.updateUserAccountBalance(this.account.balance - this.room.parameters.bet * 2)
    }
  }
}
</script>

<style scoped>

</style>