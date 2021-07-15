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
      <img src="/images/table.png" class="poker_table"/>
      <div id="opponent-hands" class="d-flex justify-space-around mt-2">
        <hand
            v-for="(opponent, i) in players"
            :key="i"
            :cards="opponent.user_id == user.id ? opponent.cards : [null, null]"
            :score="opponent.score"
            :result="opponent.score > 0 && !playing ? resultMessage(opponent) : opponent.result"
            :result-class="resultClass(opponent)"
            :bet="opponent.bet"
            :win="opponent.win"
            :id="createId(i)"
        >
          <template v-slot:title>
            <div class="font-weight-thin text-center mb-2 ml-n10 ml-lg-0">
              {{ isDealer(opponent.id) ? 'Dealer' : opponent.name }}
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
            <div class="font-weight-thin text-center mb-2 ml-n10 ml-lg-0">
              <p v-if="playersBet[i]">
                <span class="coin">{{ playersBet[i] }}</span>
                <v-icon class="coin-icon">mdi-currency-usd-circle</v-icon>
              </p>
            </div>
          </template>
        </hand>
      </div>
      <actions :room="room" :provably-fair-game="provablyFairGame" :user="user"></actions>
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

export default {
  name: 'CasinoHoldem',

  components: {GameRoom, PlayControls, Hand, Chat, Actions},

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
      foldPlayers: [],
      playersBet: {
        0: 0,
        1: 0,
        2: 0,
      },
      player: {},
      opponents: {},
      time: null,
      intervalId: null,
    }
  },

  computed: {
    ...mapState('auth', ['account', 'user']),
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
    room(room) {
      this.playersBet["1"] = this.room.parameters.bet;
      this.playersBet["2"] = this.room.parameters.bet * 2;
    },
  },
  created() {
    // it's important to wait until next tick to ensure config computed property is updated
    // after switching from one game page to another.
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
    createId(index) {
      const opponentCount = Object.keys(this.opponents).length;
      let id = "opponent_";
      if (index == 2 && opponentCount == 8) {
        return id + "_" + index;
      }
      if (index == 9 && opponentCount == 8) {
        return id + "_" + index;
      }
      return id + index;
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
}
#player_actions{
    position: absolute;
    bottom: 2%;
    left: 50%;
    transform: translate(-50%, -50%);
}
</style>
