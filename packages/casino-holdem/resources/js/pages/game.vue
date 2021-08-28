<template>
  <div class="d-flex flex-column fill-height">

    <game-room
        :playing="playing"
        @room="onRoomChange($event.room)"
        @game="onGame($event.game)"
        @event="onEvent($event.event)"
        @players="onPlayers($event.players)"
        @player-joined="onPlayerJoined($event.player)"
        @player-left="onPlayerLeft($event.player)"
        @ready="ready = $event.ready"
        @exit="onExit"
    />
    <template v-if="room">
      <div id="pot" class="d-flex justify-space-around mt-2">
        <div class="font-weight-thin text-center mb-2 ml-n10 ml-lg-0">
          <p v-if="gameRoom.pot > 0" class="bet_bg">
             <span class="coin">Pot : {{ gameRoom.pot }}</span>
            <v-icon class="coin-icon">mdi-currency-usd-circle</v-icon>
          </p>
        </div>
      </div>
      <img src="/images/table.png" class="poker_table"/>
      <div id="opponent-hands" class="d-flex justify-space-around mt-2">
        <div class="position-player">
          <hand
              v-for="(opponent, i) in players"
              :key="i"
              :cards="opponent.user_id == user.id ? opponent.cards : getCards(opponent.user_id)"
              :score="opponent.score"
              :result="opponent.score > 0 && !playing ? resultMessage(opponent) : opponent.result"
              :result-class="resultClass(opponent)"
              :bet="opponent.bet"
              :win="opponent.win"
              :class="`room-${players.length}-players position_player_${getPlayerPosition(players, opponent, i+1)}`"
          >
            <template v-slot:title>
              <div class="font-weight-thin text-center ml-n10 dealer_or_player">
                <span v-if="isFoldPlayer(opponent.user_id)">
                    Fold
                </span>
                <img v-else-if="isDealer(opponent.id)" src="/images/dealer.png" class="dealer_img"/>
                <span v-else>
                 {{ opponent.name }}
                </span>
                <v-progress-circular
                    v-show="isOpponentTurn(opponent)"
                    :rotate="360"
                    :size="25"
                    :width="2"
                    :value="isOpponentTurn(opponent) ? Math.round(100 * (opponent.action_end - time) / actionDuration) : 0"
                    color="primary"
                >
                  {{ opponent.action_end - time }}
                </v-progress-circular>
              </div>
            </template>
            <template v-slot:bottom>
              <div>
                <countdown v-if="opponent.user_id == gameRoom.action_index && gameRoom.round <= 4" :left-time="20000" @finish="finishCountdown">
                  <template slot="process" slot-scope="{ timeObj }">
                    <v-progress-linear
                        class="progress-bar"
                        color="light-blue"
                        height="10"
                        buffer-value="100"
                        :value="timeObj.ceil.s * 5"
                        striped
                    ></v-progress-linear>
                  </template>
                </countdown>
                <v-progress-linear
                    v-else
                    class="progress-bar"
                    color="light-blue"
                    height="10"
                    buffer-value="100"
                    :value="0"
                    striped
                ></v-progress-linear>
                <p v-if="gameRoom.bets && gameRoom.bets[opponent.user_id] > 0" class="bet_bg bet_bg_player">
                  <span class="coin">{{ gameRoom.bets[opponent.user_id] }}</span>
                  <v-icon class="coin-icon">mdi-currency-usd-circle</v-icon>
                </p>
              </div>
            </template>
          </hand>
        </div>
      </div>
      <div id="community-card" class="d-flex justify-center mt-2" v-if="gameRoom.community_card && gameRoom.round >= 2">
        <playing-card
            v-for="(card, i) in gameRoom.community_card"
            :key="`card-${i}`"
            :card="card"
            :clickable="false"
        >
          <template v-slot:top>
            <slot v-if="$scopedSlots['top.' + i]" :name="`top.${i}`" />
          </template>
        </playing-card>
      </div>
      <actions v-if="room && gameRoom && gameRoom.players && gameRoom.round <= 4 && user.id == gameRoom.action_index" :room="room" :provably-fair-game="provablyFairGame" :user="user"></actions>
    </template>

    <chat v-if="room" v-model="chatDrawer" :room-id="room.id" class="chat"/>
  </div>
</template>

<script>
import axios from 'axios'
import {mapState, mapActions} from 'vuex'
import FormMixin from '~/mixins/Form'
import GameRoomMixin from '~/mixins/Holdem/GameRoom'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import Hand from '~/components/Games/Cards/Hand'
import {sleep, time, get} from '~/plugins/utils'
import clickSound from '~/../audio/common/click.wav'
import dealSound from 'packages/casino-holdem/resources/audio/deal.wav'
import swooshSound from 'packages/casino-holdem/resources/audio/swoosh.wav'
import flipSound from 'packages/casino-holdem/resources/audio/flip.wav'
import winSound from 'packages/casino-holdem/resources/audio/win.wav'
import loseSound from 'packages/casino-holdem/resources/audio/lose.wav'
import pushSound from 'packages/casino-holdem/resources/audio/push.wav'
import PlayControls from '~/components/Games/HoldemPlayControls'
import GameRoom from '~/components/Games/GameRoom'
import Chat from '~/components/Chat'
import Form from "vform";
import Actions from "../../../../../resources/js/mixins/Holdem/Actions";
import PlayingCard from "../../../../../resources/js/components/Games/Cards/PlayingCard";
// import Countdown from '@choujiaojiao/vue2-countdown'
// import vueAwesomeCountdown from 'vue-awesome-countdown'

export default {
  name: 'CasinoHoldem',

  components: {GameRoom, PlayControls, Hand, Chat, Actions, PlayingCard},

  mixins: [FormMixin, GameMixin, SoundMixin, GameRoomMixin],

  data() {
    return {
      actions: [
        {name: 'fold', disabled: false, loading: false}, // $t('Fold')
        {name: 'call', disabled: false, loading: false}, // $t('Call')
        {name: 'raise', disabled: false, loading: false} // $t('Call')
      ],
      playing: false,
      initialBet: 0,
      anteBet: 0,
      bonusBet: 0,
      win: 0,
      netWin: 1,
      chatDrawer: true,
      ready: false,
      room: null,
      game: null,
      // action: null,
      loading: false,
      player: {},
      opponents: [
      ],
      time: null,
      intervalId: null,
      primaryUserIndex:false
    }
  },
  computed: {
    ...mapState('broadcasting', ['echo']),
    ...mapState('auth', ['account', 'user']),
    ...mapState('game-room', ['foldPlayers', 'playersBet', 'communityCard', 'action', 'gameRoom']),
    playerResultClass() {
      return this.netWin > 0 || this.playing ? 'primary text--primary' : (this.netWin < 0 ? 'error' : 'warning')
    },
    dealerResultClass() {
      return this.netWin < 0 ? 'primary text--primary' : (this.netWin > 0 ? 'error' : 'warning')
    },
    actionDuration() {
      return parseInt(config('multiplayer-blackjack.action_duration'), 10)
    },
    finalHitThreshold() {
      return parseInt(config('multiplayer-blackjack.final_hit_threshold'), 10)
    },
    cancelThreshold() {
      return parseInt(config('multiplayer-blackjack.cancel_threshold'), 10)
    },
    isPlayerTurn() {
      return this.time && this.player.action_start && this.player.action_end && this.player.action_start <= this.time && this.time < this.player.action_end
    },
    bet() {
      return this.room ? this.room.parameters.bet : 0
    },
    playersCount() {
      return this.room ? parseInt(this.room.parameters.players_count, 10) : 0
    },
    balanceIsSufficient() {
      return this.account.balance >= this.bet
    },
    winnersCount() {
      return [this.player.win || 0, ...Object.keys(this.opponents).map(id => this.opponents[id].win)].filter(win => win > 0).length
    },
    cancelAllowed() {
      return this.playing
          && this.opponents
          && this.time
          && this.game
          && this.game.created < this.time - this.cancelThreshold
          && Object.keys(this.opponents).filter(id => this.opponents[id].cards[0] !== null).length < this.playersCount - 1
    }
  },
  watch: {
    time(time, prevTime) {
      if (this.playing && !this.action && time === this.player.action_end && prevTime === this.player.action_end - 1) {
        this.doAction('stand')
      }
    },
  },
  created() {
    this.$nextTick(() => {
      this.bonusBet = this.defaultBonusBet
    });
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set',
    }),
    isDealer(playerId) {
      if (this.players.length >= 3) {
        for (let i = 1; i <= this.players.length; i++) {
          if (this.players[i - 1].id == playerId && i == 1) {
            return true;
          }
        }
      }

      return false;
    },
    createId(index,opponent) {
      let id = "opponent_";
      if(opponent.user_id == this.user.id){
        this.primaryUserIndex = index;
        return "primary_user";
      }
      if(this.primaryUserIndex && Number(this.primaryUserIndex) < Number(index)){
      const decrementIndex = Number(index)-1;
      id =id+decrementIndex
      }else{
      id =id+index;
      }
      return id;
    },
    isBigBlind(playerId) {
      if (this.players.length >= 3) {
        for (let i = 1; i <= this.players.length; i++) {
          if (this.players[i - 1].id == playerId && i == 3) {
            return true;
          }
        }

        return false;
      } else {
        for (let i = 1; i <= this.players.length; i++) {
          if (this.players[i - 1].id == playerId && i == 2) {
            return true;
          }
        }

        return false;
      }
    },
    isSmallBlind(playerId) {
      if (this.players.length > 2) {
        for (let i = 1; i <= this.players.length; i++) {
          if (this.players[i - 1].id == playerId && i == 2) {
            return true;
          }
        }

        return false;
      } else {
        for (let i = 1; i <= this.players.length; i++) {
          if (this.players[i - 1].id == playerId && i == 1) {
            return true;
          }
        }

        return false;
      }
    },
    play(bet) {
      this.loading = true
      this.playing = true

      this.anteBet = bet

      this.action('play', {
        bet,
        bonus_bet: this.bonusBet,
        is_big_blind: this.isBigBlind(),
        is_small_blind: this.isSmallBlind(),
        round: this.round
      }).then(() => {
        this.loading = false
      })
    },
    isOpponentTurn(opponent) {
      return this.time && opponent.action_start && opponent.action_end && opponent.action_start <= this.time && this.time <= opponent.action_end
    },
    isActionDisabled(action) {
      return !!this.action
          || (action === 'hit' && this.player.score >= 21)
          || !this.isPlayerTurn
          || Object.keys(this.opponents).length < this.playersCount - 1
    },
    enableActionTimeInterval() {
      if (!this.intervalId) {
        this.time = time()

        this.intervalId = setInterval(() => {
          this.time++
        }, 1000)
      }
    },
    resultClass(player) {
      return player.score > 0 ? (player.win > 0 ? (this.winnersCount === 1 ? 'primary text--primary' : 'warning') : 'error') : 'secondary'
    },
    resultMessage(player) {
      return player.win > 0
          ? (player.score === 21 && player.cards.length === 2
              ? this.$t('Blackjack')
              : (this.winnersCount === 1
                  ? this.$t('Win')
                  : this.$t('Push')))
          : this.$t('Lose')
    },
    onRoomChange(room) {
      this.room = room;
    },
    onPlayers(players) {
      this.$store.dispatch('game-room/onPlayers', {
        hash: this.provablyFairGame.hash,
        players: players,
        room_id: this.room.id,
      })
    },
    onPlayerJoined(player) {
    },
    onPlayerLeft(player) {
      axios.post('/api/games/casino-holdem/left', {
        hash: this.provablyFairGame.hash,
        room_id: this.room.id,
        player: player
      });
    },
    // called when the page is refreshed
    onGame(game) {
      this.game = game

      // get time when the game should finish
      const scheduledCompletionTime = get(game, 'gameable.player_hand') && get(game, 'gameable.opponent_hands')
          ? Math.max(
              game.gameable.player_hand.action_end || 0,
              ...Object.keys(game.gameable.opponent_hands).map(id => game.gameable.opponent_hands[id].action_end)
          )
          : null

      // complete the game explicitly if this time is in the past
      if (scheduledCompletionTime && time() > scheduledCompletionTime) {
        this.doAction('complete')
      }

      if (!game.is_completed) {
        this.playing = true
      }
    },
    onEvent(event) {
      if (event.action !== 'cancel') {
        // loop through player hands
        Object.keys(event.gameable.hands).forEach(userId => {
          // if the hand belongs to the current user
          if (this.user.id === parseInt(userId, 10)) {
            this.updatePlayerHand(this.player, {
              action_start: event.gameable.hands[userId].action_start,
              action_end: event.gameable.hands[userId].action_end,
              win: event.gameable.hands[userId].win
            })
            // if the hand DOES NOT belong to the current user
          } else if (typeof this.opponents[userId] !== 'undefined') {
            // deal sound
            if (event.gameable.hands[userId].cards.length > this.opponents[userId].cards.length) {
              this.sound(dealSound)
            } else if (event.game.is_completed) {
              this.sound(flipSound)
            }
            // set the opponent hand
            this.opponents[userId] = {...this.opponents[userId], ...event.gameable.hands[userId]}
          }
        })
      }

      // if game is completed
      if (event.game.is_completed) {
        this.playing = false

        // push
        if (this.winnersCount > 1) {
          this.sound(pushSound)
          this.updateUserAccountBalance(this.account.balance + this.player.win)
          // player wins
        } else if (this.player.win > 0) {
          this.sound(winSound)
          this.updateUserAccountBalance(this.account.balance + this.player.win)
          // player loses
        } else {
          this.sound(loseSound)
        }
        // if game is cancelled
      } else if (event.game.is_cancelled) {
        this.playing = false
      } else {
        this.enableActionTimeInterval()
      }
    },
    onExit() {
      this.game = null
      this.player = {}
      this.opponents = {}
      this.playing = false
    }

  }
}
</script>
<style lang="scss" scoped>
.bonus-bet-input {
  max-width: 160px;
}

.coin {
  margin-top: 5px;
  font-weight: bold;
}

.coin-icon {
  color: red;
}

.relative {
  position: relative;
}

.poker_table {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 65% !important;
}
#player_actions{
    position: absolute;
    bottom: -2%;
    left: 50%;
    transform: translate(-50%, -50%);
}
#community-card{
position: absolute;
top: 40%;
left: 50%;
transform: translate(-50%, 0);
}
#pot {
    position: absolute;
    top: 33%;
    left: 50%;
    transform: translate(-50%, 0);
    z-index: 9;
}
.bet_bg{
  background: white;
  border-radius: 20px;
  padding: 0 5px;
  span{
    color:black;
    font-size: 13px;
  }
  .coin-icon{
    color: black;
    font-size: 20px;
  }
}
.bet_bg_player{
display: inline;
}
.dealer_or_player{
margin-left: 15px!important;
}
#primary_user > #dealer_or_player{
margin-left: -12px!important;
}
.dealer_img{
  height: 25px;
  transform: rotate(351deg);
}
.dealer_opponent{
      display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 5px;
    img{
          padding-right: 5px;
    }
}

.progress-bar {
  width: 90%;
  display: block;
  height: 10px;
}

//  position player
.poker_table {
  //background: red;
  border-radius: 60%;
  display: block;
  width: 600px !important;
  height: 400px !important;
}

.position-player {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 1000px;
  height: 600px;
  //background: #ff000042;
}

// 1 player
.room-1-players.position_player_1 {
  position: absolute;
  bottom: 16px;
  left: 410px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

// 2 player
.room-2-players.position_player_1 {
  position: absolute;
  bottom: 16px;
  left: 410px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-2-players.position_player_2 {
  position: absolute;
  left: 400px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

// 3 players
.room-3-players.position_player_1 {
  position: absolute;
  bottom: 16px;
  left: 410px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-3-players.position_player_2 {
  position: absolute;
  left: 40px;
  top: 100px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-3-players.position_player_3 {
  position: absolute;
  right: 6px;
  top: 100px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

// 4 players
.room-4-players.position_player_1 {
  position: absolute;
  bottom: 16px;
  left: 410px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-4-players.position_player_2 {
  position: absolute;
  left: 20px;
  top: 230px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-4-players.position_player_3 {
  position: absolute;
  top: 5px;
  margin: 0px auto;
  display: block;
  width: 200px;
  left: 440px;
}

.room-4-players.position_player_4 {
  position: absolute;
  right: 20px;
  top: 230px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

// 5 players
.room-5-players.position_player_1 {
  position: absolute;
  bottom: 16px;
  left: 410px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-5-players.position_player_2 {
  position: absolute;
  top: 300px;
  left: 20px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-5-players.position_player_3 {
  position: absolute;
  top: 20px;
  left: 165px;
  display: block;
  width: 200px;
}

.room-5-players.position_player_4 {
  position: absolute;
  right: 165px;
  top: 10px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-5-players.position_player_5 {
  position: absolute;
  top: 300px;
  right: 10px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

// 6 players
.room-6-players.position_player_1 {
  position: absolute;
  bottom: 16px;
  left: 410px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-6-players.position_player_2 {
  position: absolute;
  bottom: 70px;
  left: 80px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-6-players.position_player_3 {
  position: absolute;
  top: 134px;
  left: 80px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-6-players.position_player_4 {
  position: absolute;
  left: 410px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-6-players.position_player_5 {
  position: absolute;
  bottom: 70px;
  right: 80px;
  margin: 0px auto;
  display: block;
  width: 200px;
}

.room-6-players.position_player_6 {
  position: absolute;
  top: 300px;
  right: 10px;
  margin: 0px auto;
  display: block;
  width: 200px;
}
</style>
