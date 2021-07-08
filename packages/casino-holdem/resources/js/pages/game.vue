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
      <img src="/images/table.gif" class="poker_table" />
            <div id="opponent-hands" class="d-flex justify-space-around mt-2">
        <hand
          v-for="(opponent, i) in opponents"
          :key="i"
          :cards="opponent.cards"
          :score="opponent.score"
          :result="opponent.score > 0 && !playing ? resultMessage(opponent) : opponent.result"
          :result-class="resultClass(opponent)"
          :bet="opponent.bet"
          :win="opponent.win"
          :id="craeteId(i)"
        >
          <template v-slot:title>
            <div class="font-weight-thin text-center mb-2 ml-n10 ml-lg-0">
              {{ isFirstJoiner(i) ? 'Dealer' : opponent.name }}
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
              <p v-if="room.big_blind && room.small_blind.user_id == i">
                <span class="coin">{{ room.parameters.bet * 1 }}</span>
                <v-icon class="coin-icon">mdi-currency-usd-circle</v-icon>
              </p>
              <p v-if="room.big_blind && room.big_blind.user_id == i">
                <span class="coin">{{ room.parameters.bet * 2 }}</span>
                <v-icon class="coin-icon">mdi-currency-usd-circle</v-icon>
              </p>
            </div>
          </template>
        </hand>
      </div>
      <div class="d-flex justify-center fill-height align-center">
        <hand
          v-if="player.cards"
          :cards="player.cards"
          :score="player.score"
          :result="player.score > 0 && !playing ? resultMessage(player) : player.result"
          :result-class="resultClass(player)"
          :bet="player.bet"
          :win="player.win"
          :player="true"
        >
          <template v-slot:title>
            <div class="font-weight-thin text-center mb-2 ml-n10 ml-lg-0">
              {{ room.dealer && user.id == room.dealer.user_id ? 'Dealer' : user.name }}
            </div>
          </template>
          <template v-slot:bottom>
            <div class="font-weight-thin text-center mb-2 ml-n10 ml-lg-0">
              <p v-if="room.small_blind && room.small_blind.user_id == user.id">
                <span class="coin relative">{{ room.parameters.bet * 1 }}</span>
                <v-icon class="coin-icon">mdi-currency-usd-circle</v-icon>
              </p>
              <p v-if="room.big_blind && room.big_blind.user_id == user.id">
                <span class="coin relative">{{ room.parameters.bet * 2 }}</span>
                <v-icon class="coin-icon">mdi-currency-usd-circle</v-icon>
              </p>
            </div>
          </template>
        </hand>
      </div>
    <!-- <hand
      :cards="dealer.cards"
      :result="dealer.result"
      :inactive-cards="winCards.length && !playing ? dealer.cards.filter(card => winCards.indexOf(card) === -1) : []"
      class="d-flex justify-center"
      :result-class="dealerResultClass"
    />
    <div class="d-flex justify-space-around fill-height align-center">
      <hand
        :cards="community.cards"
        :inactive-cards="winCards.length ? community.cards.filter(card => winCards.indexOf(card) === -1) : []"
      />
    </div>
    <div class="d-flex justify-center align-center">
      <hand
        :cards="player.cards"
        :bet="bet"
        :win="win"
        :inactive-cards="winCards.length ? player.cards.filter(card => winCards.indexOf(card) === -1) : []"
        :result="player.result"
        :result-class="playerResultClass"
      />
    </div>
    <div class="d-flex justify-center flex-wrap mt-10">
      <v-btn
        v-for="a in actions"
        :key="a.name"
        :disabled="!provablyFairGame.hash || a.disabled"
        :loading="a.loading"
        class="mx-1 my-2 my-lg-0"
        small
        @click="action(a.name)"
      >
        {{ $t(a.name) }}
      </v-btn>
    </div> -->
    <play-controls :bet-label="$t('Ante bet')" :disabled="account.balance < initialBet + bonusBet" :loading="loading" :playing="playing" @bet-change="initialBet = $event" @play="play(bet)">
      <template v-slot:after-bet-input>
        <v-text-field
          v-model.number="bonusBet"
          :label="$t('Bonus bet')"
          dense
          :rules="!isNaN(minBonusBet) && !isNaN(maxBonusBet) ? [validationInteger, v => validationMin(v, minBonusBet), v => validationMax(v, maxBonusBet)] : []"
          :disabled="playing"
          outlined
          :full-width="false"
          class="bonus-bet-input text-center ml-md-2"
        >
          <template v-slot:prepend-inner>
            <v-btn small text icon color="primary" @click="bonusBet = Math.max(minBonusBet, bonusBet - bonusBetStep)">
              <v-icon small>
                mdi-minus
              </v-icon>
            </v-btn>
          </template>
          <template v-slot:append>
            <v-btn small text icon color="primary" @click="bonusBet = Math.min(maxBonusBet, bonusBet + bonusBetStep)">
              <v-icon small>
                mdi-plus
              </v-icon>
            </v-btn>
          </template>
        </v-text-field>
      </template>
    </play-controls>
    </template>
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import Hand from '~/components/Games/Cards/Hand'
import { sleep,time,get } from '~/plugins/utils'
import clickSound from '~/../audio/common/click.wav'
import dealSound from 'packages/casino-holdem/resources/audio/deal.wav'
import swooshSound from 'packages/casino-holdem/resources/audio/swoosh.wav'
import flipSound from 'packages/casino-holdem/resources/audio/flip.wav'
import winSound from 'packages/casino-holdem/resources/audio/win.wav'
import loseSound from 'packages/casino-holdem/resources/audio/lose.wav'
import pushSound from 'packages/casino-holdem/resources/audio/push.wav'
import PlayControls from '~/components/Games/PlayControls'
import GameRoom from '~/components/Games/GameRoom'

export default {
  name: 'CasinoHoldem',

  components: {GameRoom, PlayControls, Hand },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      actions: [
        { name: 'fold', disabled: true, loading: false }, // $t('Fold')
        { name: 'call', disabled: true, loading: false } // $t('Call')
      ],
      playing: false,
      initialBet: 0,
      anteBet: 0,
      bonusBet: 0,
      win: 0,
      netWin: 1,

      dealer: {
        cards: ['C5', 'DT'],
        result: this.$t('Two pair')
      },

      community: {
        cards: ['DJ', 'HK', 'H9', 'S5', 'HT']
      },

      winCards: ['HK', 'H9', 'HA', 'H4', 'HT'],
      ready: false,
      room: null,
      game: null,
      // action: null,
      loading: false,
      defaultHand: {
        name: '',
        cards: [],
        score: -1,
        result: '',
        bet: 0,
        win: 0
      },
      player: {},
      opponents: {
        // 2: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
        // 3: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
        // 3: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
        // 4: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
        // 5: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
        // 6: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
        // 7: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
        // 8: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
        // 9: {
        //   name: "test",
        //   cards: ["C5", "DT"],
        //   score: -1,
        //   result: "",
        //   bet: 0,
        //   win: 0,
        // },
      },
      time: null,
      intervalId: null,
      round: 0, // 0: init, prelFop: 1, flop: 2, turn: 3, river: 4
    }
  },

  computed: {
    ...mapState('auth', ['account','user']),
    defaultBonusBet () {
      return parseInt(this.config.default_bonus_bet_amount, 10)
    },
    minBonusBet () {
      return parseInt(this.config.min_bonus_bet, 10)
    },
    maxBonusBet () {
      return parseInt(this.config.max_bonus_bet, 10)
    },
    bonusBetStep () {
      return parseInt(this.config.bonus_bet_change_amount, 10)
    },
    playerResultClass () {
      return this.netWin > 0 || this.playing ? 'primary text--primary' : (this.netWin < 0 ? 'error' : 'warning')
    },
    dealerResultClass () {
      return this.netWin < 0 ? 'primary text--primary' : (this.netWin > 0 ? 'error' : 'warning')
    },
       actionDuration () {
      return parseInt(config('multiplayer-blackjack.action_duration'), 10)
    },
    finalHitThreshold () {
      return parseInt(config('multiplayer-blackjack.final_hit_threshold'), 10)
    },
    cancelThreshold () {
      return parseInt(config('multiplayer-blackjack.cancel_threshold'), 10)
    },
    isPlayerTurn () {
      return this.time && this.player.action_start && this.player.action_end && this.player.action_start <= this.time && this.time < this.player.action_end
    },
    bet () {
      return this.room ? this.room.parameters.bet : 0
    },
    playersCount () {
      return this.room ? parseInt(this.room.parameters.players_count, 10) : 0
    },
    balanceIsSufficient () {
      return this.account.balance >= this.bet
    },
    winnersCount () {
      return [this.player.win || 0, ...Object.keys(this.opponents).map(id => this.opponents[id].win)].filter(win => win > 0).length
    },
    cancelAllowed () {
      return this.playing
        && this.opponents
        && this.time
        && this.game
        && this.game.created < this.time - this.cancelThreshold
        && Object.keys(this.opponents).filter(id => this.opponents[id].cards[0] !== null).length < this.playersCount - 1
    }
  },
  watch: {
    time (time, prevTime) {
      if (this.playing && !this.action && time === this.player.action_end && prevTime === this.player.action_end - 1) {
        this.doAction('stand')
      }
    },
    playing (playing, wasPlaying) {
      // clear action time interval when the game is completed
      if (wasPlaying && !playing) {
        this.clearActionTimeInterval()
      }
    }
  },
  created () {
    // it's important to wait until next tick to ensure config computed property is updated
    // after switching from one game page to another.
    this.$nextTick(() => {
      this.bonusBet = this.defaultBonusBet
    })
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
      setProvablyFairGame: 'provably-fair/set'
    }),
    craeteId(index) {
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
    isBigBlind() {
        return this.room.big_blind && this.room.big_blind.user_id == this.user.id;
    },
    isSmallBlind() {
      return this.room.small_blind && this.room.small_blind.user_id == this.user.id;
    },
    play (bet) {
      this.loading = true
      this.playing = true

      this.anteBet = bet

      this.action('play', { bet, bonus_bet: this.bonusBet, is_big_blind:  this.isBigBlind(), is_small_blind: this.isSmallBlind(), round: this.round}).then(() => { this.loading = false })
    },
    initPlayerCard() {
      this.player.cards = []
      let animationDelay = 0
      setTimeout(() => {
        this.player.cards.push(this.room.gameable.player_cards[0], this.room.gameable.player_cards[1]);
        this.sound(dealSound);
      }, animationDelay += 1500)
    },
    // handle game actions (deal, hit, stand etc)
    async action (name, params = {}) {
      if (name !== 'play') {
        this.sound(clickSound)
      }

      // disable all actions
      this.actions.forEach(action => {
        action.disabled = true
        action.loading = action.name === name
      })

      // update user balance
      if (name === 'call') {
        this.bet += this.anteBet * 2
        this.updateUserAccountBalance(this.account.balance - this.anteBet * 2)
      }

      this.player.result = ''

      // clear previous game results
      if (name === 'play') {
        this.bet = 0
        this.win = 0
        setTimeout(() => { this.netWin = 0 }, 500)

        this.player.cards = []
        this.dealer.cards = []
        this.community.cards = []
        this.winCards = []

        this.dealer.result = ''

        this.updateUserAccountBalance(this.account.balance - this.anteBet)

        this.sound(swooshSound)

        await sleep(500)
      }

      // API request params
      const endpoint = this.getRoute(name)
      const requestParams = { hash: this.provablyFairGame.hash, ...params }

      // execute the action
      const { data: game } = await axios.post(endpoint, requestParams)

      let animationDelay = 0

      // initial draw
      if (!game.is_completed) {
        this.bet = game.bet

        // 1st player card
        this.player.cards.push(game.gameable.player_cards[0])
        this.sound(dealSound)

        // 1st dealer card
        setTimeout(() => {
          this.dealer.cards.push(null)
          this.sound(dealSound)
        }, animationDelay += 500)

        // 2nd player card
        setTimeout(() => {
          this.player.cards.push(game.gameable.player_cards[1])
          this.sound(dealSound)
        }, animationDelay += 500)

        // 2nd dealer card
        setTimeout(() => {
          this.dealer.cards.push(null)
          this.sound(dealSound)
        }, animationDelay += 500)

        // deal 3 community cards
        game.gameable.community_cards.forEach(card => {
          setTimeout(() => {
            this.community.cards.push(card)
            this.sound(dealSound)
          }, animationDelay += 500)
        })

        // display player hand rank
        setTimeout(() => {
          this.player.result = game.gameable.player_hand_title
          this.winCards = game.gameable.player_combination_cards

          // show action buttons
          this.actions.forEach(action => {
            // if user has sufficient funds to place call bet
            if (action.name !== 'call' || this.account.balance >= this.anteBet * 2) {
              action.disabled = false
            }
          })
        }, animationDelay)

        setTimeout(() => {
          this.winCards = []
        }, animationDelay += 2500)
      // game completed
      } else {
        this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })

        this.actions.forEach(action => {
          setTimeout(() => {
            action.loading = false
          }, animationDelay)
        })

        animationDelay += 250

        if (game.gameable.call_bet) {
          // deal 2 more community cards
          game.gameable.community_cards.slice(3).forEach(card => {
            setTimeout(() => {
              this.community.cards.push(card)
              this.sound(dealSound)
            }, animationDelay += 500)
          })

          // show dealer cards
          game.gameable.dealer_cards.forEach((card, i) => {
            setTimeout(() => {
              // direct assignment causes animation strange delay, so using splice instead
              this.dealer.cards.splice(i, 1, card)
              this.sound(flipSound)
            }, animationDelay += 500)
          })
        }

        // display dealer score and result for each hand
        setTimeout(() => {
          this.win = game.win
          this.netWin = game.win - game.bet

          this.player.result = game.gameable.call_bet ? game.gameable.player_hand_title : this.$t('Fold')

          if (game.gameable.call_bet) {
            this.dealer.result = game.gameable.dealer_hand_title
          }

          this.playing = false

          // update balance
          this.updateUserAccountBalance(game.account.balance)

          if (game.gameable.call_bet) {
            if (game.gameable.call_win > 0) {
              this.winCards = game.gameable.player_combination_cards
            } else if (game.gameable.call_win === 0) {
              this.winCards = game.gameable.dealer_combination_cards
            }
          }

          // play sound
          if (this.netWin > 0) {
            this.sound(winSound)
          } else if (this.netWin === 0) {
            this.sound(pushSound)
          } else {
            this.sound(loseSound)
          }
        }, animationDelay += 500)
      }
    },
    isFirstJoiner(i) {
      if (!this.room.dealer) {
        return false;
      }

      return this.room.dealer.user_id == i;
    },
    isOpponentTurn (opponent) {
      return this.time && opponent.action_start && opponent.action_end && opponent.action_start <= this.time && this.time <= opponent.action_end
    },
    updatePlayerHand (player, values) {
      Object.keys(values).forEach(key => {
        player[key] = values[key]
      })
    },
    // handle game actions (play, hit, stand)
    async doAction (action) {
      this.action = action

      // clear previous game results
      if (action === 'play') {
        this.sound(swooshSound)
        this.playing = true
        this.updateUserAccountBalance(this.account.balance - this.bet)

        // assign default hand values (remove existing cards)
        this.player = { ...this.defaultHand }

        for (const userId in this.opponents) {
          if (this.opponents[userId].score > 0) {
            this.opponents[userId] = { ...this.defaultHand, name: this.opponents[userId].name }
            setTimeout(() => this.updatePlayerHand(this.opponents[userId], { cards: [null, null] }), 200)
          }
        }
      } else {
        this.sound(clickSound)
      }

      // execute the action
      const { data: game } = await axios.post(this.getRoute(action).replace('{room}', `${this.room.id}`))

      this.game = game

      // deal sound
      if (action === 'play' || action === 'hit') {
        this.sound(dealSound)
      }

      if (action !== 'cancel') {
        // update player hand
        this.player = { ...this.player, ...game.gameable.player_hand }
      } else {
        // assign default hand values (remove existing cards)
        this.updatePlayerHand(this.player, { ...this.defaultHand, cards: [null, null], result: this.$t('Cancelled') })
        this.updateUserAccountBalance(this.account.balance + game.gameable.player_hand.win)
      }

      // enable action buttons
      this.action = null

      // enable action time interval
      this.enableActionTimeInterval()
    },
    isActionDisabled (action) {
      return !!this.action
        || (action === 'hit' && this.player.score >= 21)
        || !this.isPlayerTurn
        || Object.keys(this.opponents).length < this.playersCount - 1
    },
    enableActionTimeInterval () {
      if (!this.intervalId) {
        this.time = time()

        this.intervalId = setInterval(() => {
          this.time++
        }, 1000)
      }
    },
    clearActionTimeInterval () {
      if (this.intervalId) {
        clearInterval(this.intervalId)
        this.intervalId = null
        this.time = null
      }
    },
    resultClass (player) {
      return player.score > 0 ? (player.win > 0 ? (this.winnersCount === 1 ? 'primary text--primary' : 'warning') : 'error') : 'secondary'
    },
    resultMessage (player) {
      return player.win > 0
        ? (player.score === 21 && player.cards.length === 2
          ? this.$t('Blackjack')
          : (this.winnersCount === 1
            ? this.$t('Win')
            : this.$t('Push')))
        : this.$t('Lose')
    },
    onRoomChange (room) {
      this.room = room
      this.initPlayerCard();
    },
    onPlayers (players) {
      // loop through player hands
      players.forEach(player => {
        // if the hand belongs to the current user
        if (this.user.id === player.id) {
          // set the player hand to default values
          this.player = { ...this.defaultHand }
          // update player hand to display cards
          setTimeout(() => {
            this.updatePlayerHand(
              this.player,
              this.game
                ? { ...get(this.game, 'gameable.player_hand') }
                : { cards: [], result: players.length === this.playersCount ? this.$t('Click Play') : this.$t('Waiting')
              }
            )

            if (this.playing) {
              this.enableActionTimeInterval()
            }
          }, 100)
        // if the hand DOES NOT belong to the current user
        } else {
          // set the opponent hand to default values
          this.$set(this.opponents, player.id, { ...this.defaultHand, name: player.name })
          // update opponent hand to display values
          setTimeout(() => this.updatePlayerHand(this.opponents[player.id],
            get(this.game, 'gameable.opponent_hands')
              ? { ...get(this.game, 'gameable.opponent_hands.' + player.id) }
              : { cards: [null, null] }
          ), 100)
        }
      })
    },
    onPlayerJoined (player) {
      // if this player didn't join earlier
      if (!this.opponents[player.id]) {
        this.$set(this.opponents, player.id, { ...this.defaultHand, name: player.name })
        setTimeout(() => this.updatePlayerHand(this.opponents[player.id], { cards: [null, null] }), 100)

        // update player message when all players joined
        if (Object.keys(this.opponents).length + 1 === this.playersCount) {
          this.updatePlayerHand(this.player, { result: this.$t('Click Play') })
        }
      // if this player already exists (probably left and joined again)
      } else {
        this.updatePlayerHand(this.opponents[player.id], { result: '' })
      }
    },
    onPlayerLeft (player) {
      // add a message when a player leaves the room (it also happens when the page is refreshed)
      this.updatePlayerHand(this.opponents[player.id], { result: this.$t('Left') })
    },
    // called when the page is refreshed
    onGame (game) {
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
    onEvent (event) {
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
            this.opponents[userId] = { ...this.opponents[userId], ...event.gameable.hands[userId] }
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
    onExit () {
      this.game = null
      this.player = {}
      this.opponents = {}
      this.playing = false
      this.clearActionTimeInterval()
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
</style>
