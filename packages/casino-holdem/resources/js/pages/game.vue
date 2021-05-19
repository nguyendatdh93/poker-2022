<template>
  <div class="d-flex flex-column fill-height py-3">
    <hand
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
    </div>
    <play-controls :bet-label="$t('Ante bet')" :disabled="account.balance < initialBet + bonusBet" :loading="loading" :playing="playing" @bet-change="initialBet = $event" @play="play">
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
  </div>
</template>

<script>
import axios from 'axios'
import { mapState, mapActions } from 'vuex'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import Hand from '~/components/Games/Cards/Hand'
import { sleep } from '~/plugins/utils'
import clickSound from '~/../audio/common/click.wav'
import dealSound from 'packages/casino-holdem/resources/audio/deal.wav'
import swooshSound from 'packages/casino-holdem/resources/audio/swoosh.wav'
import flipSound from 'packages/casino-holdem/resources/audio/flip.wav'
import winSound from 'packages/casino-holdem/resources/audio/win.wav'
import loseSound from 'packages/casino-holdem/resources/audio/lose.wav'
import pushSound from 'packages/casino-holdem/resources/audio/push.wav'
import PlayControls from '~/components/Games/PlayControls'

export default {
  name: 'CasinoHoldem',

  components: { PlayControls, Hand },

  mixins: [FormMixin, GameMixin, SoundMixin],

  data () {
    return {
      actions: [
        { name: 'fold', disabled: true, loading: false }, // $t('Fold')
        { name: 'call', disabled: true, loading: false } // $t('Call')
      ],
      loading: false,
      playing: false,

      initialBet: 0,
      anteBet: 0,
      bonusBet: 0,
      bet: 0,
      win: 0,
      netWin: 1,

      player: {
        cards: ['HA', 'H4'],
        result: this.$t('Flush')
      },

      dealer: {
        cards: ['C5', 'DT'],
        result: this.$t('Two pair')
      },

      community: {
        cards: ['DJ', 'HK', 'H9', 'S5', 'HT']
      },

      winCards: ['HK', 'H9', 'HA', 'H4', 'HT']
    }
  },

  computed: {
    ...mapState('auth', ['account']),
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
    play (bet) {
      this.loading = true
      this.playing = true

      this.anteBet = bet

      this.action('play', { bet, bonus_bet: this.bonusBet }).then(() => { this.loading = false })
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
    }
  }
}
</script>
<style lang="scss" scoped>
  .bonus-bet-input {
    max-width: 160px;
  }
</style>
