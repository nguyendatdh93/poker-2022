<template>
  <v-container class="mt-10">
    <v-row>
      <v-col class="text-center">
        <h3 class="display-1 font-weight-thin">
          {{ $t('Recent games') }}
        </h3>
      </v-col>
    </v-row>
    <v-row class="justify-center">
      <v-col cols="12" lg="8">
        <v-list v-if="recentGames === null">
          <v-skeleton-loader v-for="(v,i) in Array(10).fill(0)" :key="i" type="list-item-avatar-two-line" />
        </v-list>
        <p v-else-if="recentGames.length === 0" class="text-center">
          {{ $t('No games found') }}
        </p>
        <v-list v-else subheader>
          <v-list-item v-for="game in recentGames" :key="game.id" :to="{ name: 'history.games.show', params: { id: game.id } }">
            <v-list-item-avatar>
              <v-img :src="game.account.user.avatar_url" />
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>
                {{ game.account.user.name }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ game.title }}
              </v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-icon>
              <v-chip v-if="game.win > game.bet" color="success">
                <v-icon left small>
                  mdi-thumb-up
                </v-icon>
                {{ $t('Won') }} {{ decimal(game.win - game.bet, 2) }}
              </v-chip>
              <v-chip v-else-if="game.win < game.bet" color="error">
                <v-icon left small>
                  mdi-thumb-down
                </v-icon>
                {{ $t('Lost') }} {{ decimal(game.bet - game.win) }}
              </v-chip>
              <v-chip v-else color="warning">
                <v-icon left small>
                  mdi-thumbs-up-down
                </v-icon>
                {{ $t('Tie') }}
              </v-chip>
            </v-list-item-icon>
          </v-list-item>
        </v-list>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import { decimal } from '~/plugins/format'
import { config } from '~/plugins/config'

export default {
  data () {
    return {
      recentGames: null
    }
  },

  created () {
    this.pullRecentGames()
  },

  methods: {
    config,
    decimal,
    async pullRecentGames () {
      const { data } = await axios.get('/api/pub/games/recent')

      this.recentGames = data
    }
  }
}
</script>
