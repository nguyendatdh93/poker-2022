<script>
import {config} from '~/plugins/config'
import {route} from '~/plugins/route'
import {mapActions, mapState} from "vuex";
import Form from "vform";
import axios from "axios";

export default {
  data() {
    return {
      gamePlay: false,
    }
  },
  computed: {
    ...mapState('game-room', ['players', 'gameRoom']),
  },
  watch: {
    room(room) {
      this.echo.join(`game.${room.id}`)
          .listen('OnPlayersEvent', data => {
            if (data.left_player_id == this.user.id) {
              return window.location.href = '/';
            }

            this.$store.dispatch('game-room/setPlayers', JSON.parse(data.players))
          }).listen('GameRoomStartEvent', data => {
            this.$store.dispatch('game-room/setGameRoom', data.game_room);
            this.distributeCards();
          }).listen('GameRoomPlayEvent', data => {
            let gameRoom = JSON.parse(data.game_room);
            this.$store.dispatch('game-room/setGameRoom', gameRoom);
            this.gamePlay = true;
            if (data.user_id == this.user.id) {
              this.updateUserAccountBalance(this.account.balance - data.bet);
            }
            if (gameRoom.winner && gameRoom.winner_cards && gameRoom.winner_amount) {
              this.gameCompleted();
            }
          });
    },
  },
  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
    }),
    distributeCards() {
      $(function () {
        setTimeout(function () {
          $(".card").each(function (e) {
            setTimeout(function () {
              $(".card").eq(e).addClass("ani" + e);
            }, e * 200);
          });
        }, 2000);
      });
    },
    async finishCountdown() {
      await axios.post('/api/games/casino-holdem/fold', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
        user_id: this.gameRoom.action_index,
        user_action_index: this.getPlayerActionIndex(this.user.id)
      });
    },
    updatePlayerHand(player, values) {
      Object.keys(values).forEach(key => {
        player[key] = values[key]
      })
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
    getCards(userId) {
      if (this.gameRoom.winner && this.gameRoom.player_cards && this.gameRoom.player_cards[userId]) {
        return this.gameRoom.player_cards[userId].cards;
      }

      return [null, null];
    },
    isFoldPlayer(userId) {
      return this.gameRoom.fold_players && this.gameRoom.fold_players[userId] ? true : false;
    },
    gameCompleted() {
      if (this.user.id == this.gameRoom.winner) {
        this.updateUserAccountBalance(this.account.balance + Number(this.gameRoom.winner_amount));
      }
      axios.post('/api/games/casino-holdem/game-completed', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
      });
    },
    getPlayerPosition(players, player, currentPlayerPosition) {
      if (player.user_id == this.user.id) {
        return 0;
      }

      let currentUserPosition = players.findIndex((e) => e.user_id == this.user.id);
      if (currentPlayerPosition > currentUserPosition) {
        return currentPlayerPosition - currentUserPosition;
      } else {
        return players.length - currentUserPosition + currentPlayerPosition;
      }
    }
  }
}
</script>
