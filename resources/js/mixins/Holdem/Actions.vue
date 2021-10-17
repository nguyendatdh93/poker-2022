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

    <v-slider
        v-if="provablyFairGame.hash && !gameRoom.fold_players[user.id] && players"
        v-model="sliderBet"
        class="align-center"
        :max="getMaxSlider()"
        :min="getMinSlider()"
        hide-details
        @end="onRaiseBet"
    >
      <template v-slot:append>
        <v-text-field
            v-model="sliderBet"
            class="mt-0 pt-0"
            hide-details
            single-line
            type="number"
            style="width: 60px"
        ></v-text-field>
      </template>
    </v-slider>
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
    ...mapState('game-room', ['foldPlayers', 'gameRoom', 'players']),
  },
  data() {
    return {
      sliderBet: 1,
      raiseBet: 1,
    }
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
        bet: this.sliderBet,
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
    },
    onRaiseBet(bet) {
      this.raiseBet = bet;
    },
    getMinSlider() {
      return parseInt(this.gameRoom.previously_bet) + parseInt(Math.floor(this.gameRoom.previously_bet/2));
    },
    getMaxSlider() {
      for (let i = 0; i < this.players.length; i++) {
        if (this.players[i].user_id != this.user.id || !this.players[i].user) {
          continue;
        }

        if (!this.players[i].user.account.buy_in) {
          return 0;
        }

        return this.players[i].user.account.buy_in;
      }

      return 500;
    }
  }
}
</script>

<style scoped>

</style>