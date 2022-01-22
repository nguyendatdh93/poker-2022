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
      startCountDown: false,
      countContinue: true,
    }
  },
  computed: {
    ...mapState('game-room', ['players', 'gameRoom','collectpot','countpot','roomPot']),
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

            let potIndex = this.roomPot.findIndex(rooms => rooms.roomid == this.players[0].game_room_id);
            if(potIndex == -1){
              this.roomPot.push({'roomid': this.players[0].game_room_id, 'collectpot': 0, 'round': 1});
            }
            else
            {
              this.roomPot[potIndex].collectpot = 0;
              this.roomPot[potIndex].round = 1;
              this.countContinue = false;
            }
            this.$store.commit('game-room/GAME_ROOM_POTS', this.roomPot);
            setTimeout(function(){
              $('.poker_icon').css('opacity',0);
            }, 500);
            this.$store.dispatch('game-room/setGameRoom', data.game_room);
            this.distributeCards();
          }).listen('GameRoomPlayEvent', data => {
            let gameRoom = JSON.parse(data.game_room);
            this.$store.dispatch('game-room/setGameRoom', gameRoom);
            this.$store.dispatch('game-room/setChips', data.chips);
            if(this.countContinue)
            {
              if(data.hasOwnProperty('bet'))
              {
                let totalCountPot = parseInt(this.countpot) + parseInt(data.bet);
                let potIndex = this.roomPot.findIndex(rooms => rooms.roomid == this.players[0].game_room_id);
                if(potIndex == -1){
                  this.roomPot.push({'roomid': this.players[0].game_room_id, 'collectpot': totalCountPot, 'round': 1});
                }
                else
                {
                  this.roomPot[potIndex].collectpot = this.roomPot[potIndex].collectpot + totalCountPot;
                  if(this.roomPot[potIndex].round != gameRoom.round)
                  {
                    this.$store.commit('game-room/GAME_ROOM_COLLECT_POTS', 1);
                    this.roomPot[potIndex].round = gameRoom.round
                  }
                }
                this.$store.commit('game-room/GAME_ROOM_POTS', this.roomPot);
                $("#playerId_"+data.user_id+" .poker_icon").css('opacity',1);
                $("#playerId_"+data.user_id+" .poker_icon span").html(data.bet);
              }
            }

            if(!this.countContinue)
            {
              this.countContinue = true;
            }

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
      let self = this;
      return new Promise(function (resolve, reject) {
        setTimeout(function () {
          $(".card").each(function (e) {
            setTimeout(function () {
              let position = self.getPlayerPosition(self.players, self.players[e], e);
              $(".card").eq(e).addClass("ani" + (position));
            }, e * 200);
          });
          resolve();
        }, 2000)
      }).then(() => {
        setTimeout(function () {
          self.startCountDown = true;
        }, 2000)
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
