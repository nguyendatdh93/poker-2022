<template>
  <v-card>
    <v-toolbar>
      <v-toolbar-title>
        {{ $t('Game information') }}
      </v-toolbar-title>
      <v-spacer />
      <v-btn icon @click="$emit('close')">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <template v-slot:extension>
        <v-tabs v-model="infoTab" centered hide-slider>
          <v-tab href="#tab-about">
            {{ $t('How to play') }}
          </v-tab>
          <v-tab href="#tab-ante-paytable">
            {{ $t('Ante paytable') }}
          </v-tab>
          <v-tab href="#tab-bonus-paytable">
            {{ $t('Bonus paytable') }}
          </v-tab>
        </v-tabs>
      </template>
    </v-toolbar>
    <v-tabs-items v-model="infoTab">
      <v-tab-item value="tab-about">
        <v-card flat>
          <v-card-text class="about-text">
            <p>
              {{ $t('Casino Hold\'Em is a poker variation, similar to Texas Hold\'Em.') }}
              {{ $t('The main difference is that in Casino Hold\em you don\'t play with other players, but only against the dealer.') }}
            </p>
            <p>
              {{ $t('The Casino Hold\'em goal is to beat the Dealer\'s hand.') }}
              {{ $t('Casino Hold\'em starts with Ante bet.') }}
              {{ $t('The first step is to receive two face-up cards for you, two hidden – for the Dealer and three flop cards – again with faces up.') }}
              {{ $t('After receiving your two cards and the three "flop" cards, you have to evaluate your hand and choose among the following.') }}
            </p>
            <ul>
              <li>{{ $t('Fold – you lose your Ante bet and the game is over.') }}</li>
              <li>{{ $t('Call – you continue with an additional Call bet (equal to twice the Ante bet) and two extra community cards are dealt.') }}</li>
            </ul>
            <p></p>
            <p>
              {{ $t('When all the five flop cards are on the table, the Dealer\'s cards are turned over.') }}
              {{ $t('Your best hand is formed by a combination between your own two cards and the five flop cards.') }}
              {{ $t('The hands are compared to each other and the highest one wins the game.') }}
            </p>
            <p>
              {{ $t('You might make an additional bonus bet called AA bonus bet.') }}
              {{ $t('Bonus bet counts your two own cards and the three flop cards, dealt on the first round of the game.') }}
              {{ $t('This bet is not related to the other dealing results.') }}
              {{ $t('You might lose the game, but the bonus bet might win or vice versa.') }}
            </p>
            <p>
              {{ $t('Dealer qualifies only with a hand starting from Pair of 4 and higher.') }}
              {{ $t('If the Dealer does not qualify the Ante bet is paid according the Ante paytable and the Call bet is returned.') }}
              {{ $t('If the Dealer qualifies, and the player\'s hand is better than the dealer\'s, the Ante bet pays according to the Ante paytable and the Call bet pays 1 to 1.') }}
              {{ $t('If the dealer qualifies, and the dealer\'s hand is equal to the player\'s, all bets are push.') }}
              {{ $t('If the dealer qualifies, and the dealer\'s hand is better than the player\'s, the player loses all bets.') }}
            </p>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-ante-paytable">
        <v-card flat>
          <v-card-text>
            <v-simple-table>
              <thead>
                <tr>
                  <th>{{ $t('Hand') }}</th>
                  <th class="text-right">
                    {{ $t('Payout') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <template v-for="(hand, i) in ante.hands">
                  <tr v-if="ante.paytable[i] > 0" :key="i">
                    <td>{{ hand }}</td>
                    <td class="text-right">
                      {{ $t('bet') }} x {{ ante.paytable[i] }} ({{ ante.paytable[i] - 1 }}:1)
                    </td>
                  </tr>
                </template>
              </tbody>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-bonus-paytable">
        <v-card flat>
          <v-card-text>
            <v-simple-table>
              <thead>
                <tr>
                  <th>{{ $t('Hand') }}</th>
                  <th class="text-right">
                    {{ $t('Payout') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <template v-for="(hand, i) in bonus.hands">
                  <tr v-if="bonus.paytable[i] > 0" :key="i">
                    <td>{{ hand }}</td>
                    <td class="text-right">
                      {{ $t('bet') }} x {{ bonus.paytable[i] }} ({{ bonus.paytable[i] - 1 }}:1)
                    </td>
                  </tr>
                </template>
              </tbody>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  data () {
    return {
      infoTab: 'tab-about',
      ante: {
        paytable: config('casino-holdem.ante_paytable'),
        hands: [
          this.$t('High card'),
          this.$t('Pair'),
          this.$t('Two pair'),
          this.$t('Three of a kind'),
          this.$t('Straight'),
          this.$t('Flush'),
          this.$t('Full house'),
          this.$t('Four of a kind'),
          this.$t('Straight flush'),
          this.$t('Royal flush')
        ]
      },
      bonus: {
        paytable: config('casino-holdem.bonus_paytable'),
        hands: [
          this.$t('High card'),
          this.$t('Pair of aces'),
          this.$t('Two pair'),
          this.$t('Three of a kind'),
          this.$t('Straight'),
          this.$t('Flush'),
          this.$t('Full house'),
          this.$t('Four of a kind'),
          this.$t('Straight flush'),
          this.$t('Royal flush')
        ]
      }
    }
  }
}
</script>
