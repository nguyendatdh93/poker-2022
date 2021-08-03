<template>
  <div class="d5-flex justify-center flex-wrap mt-10" id="player_actions">
    <v-btn
        :disabled="!provablyFairGame.hash || gameRoom.fold_players[user.id]"
        class="mx-1 my-2 my-lg-0"
        small
        @click="onFold()"
    > Fold
    </v-btn>
    <v-btn
        :disabled="!provablyFairGame.hash || gameRoom.fold_players[user.id]"
        class="mx-1 my-2 my-lg-0"
        small
        @click="onCall()"
    > Call
    </v-btn>
    <v-btn
        :disabled="!provablyFairGame.hash || gameRoom.fold_players[user.id]"
        class="mx-1 my-2 my-lg-0"
        small
        @click="onRaise()"
    > Raise
    </v-btn>
    <v-btn
        :disabled="!provablyFairGame.hash || gameRoom.fold_players[user.id]"
        v-if="gameRoom.round == 2 && user.id == gameRoom.small_blind"
        class="mx-1 my-2 my-lg-0"
        small
        @click="onBet()"
    > Bet
    </v-btn>
    <v-btn
        :disabled="!provablyFairGame.hash || gameRoom.fold_players[user.id]"
        v-if="gameRoom.round >= 2 && gameRoom.player_can_check && user.id == gameRoom.player_can_check"
        class="mx-1 my-2 my-lg-0"
        small
        @click="onCheck()"
    > check
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
    ...mapState('game-room', ['foldPlayers', 'gameRoom']),
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
        user_id: this.user.id,
        user_action_index: this.getPlayerActionIndex(this.user.id)
      });
    },
    onCall() {
      axios.post('/api/games/casino-holdem/call', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
        user_id: this.user.id,
        user_action_index: this.getPlayerActionIndex(this.user.id)
      });
    },
    onRaise() {
      axios.post('/api/games/casino-holdem/raise', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
        user_id: this.user.id,
        user_action_index: this.getPlayerActionIndex(this.user.id)
      });
    },
    onCheck() {
      axios.post('/api/games/casino-holdem/check', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
        user_id: this.user.id,
        user_action_index: this.getPlayerActionIndex(this.user.id)
      });
    },
    onBet() {
      axios.post('/api/games/casino-holdem/bet', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
        user_id: this.user.id,
        user_action_index: this.getPlayerActionIndex(this.user.id)
      });
    },
    getPlayerActionIndex(playerId) {
      if (this.gameRoom.players) {
        let players = Object.values(this.gameRoom.players);
        return players.findIndex((player) => {
          return player == playerId;
        })
      }

      return -1;
    }
  }
}
</script>

<style scoped>

</style>