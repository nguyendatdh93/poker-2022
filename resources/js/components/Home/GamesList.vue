<template>
  <v-container class="mt-10">
    <v-row>
      <v-col class="text-center">
        <h3 class="display-1 font-weight-thin">
          {{ $t('Enjoy our exciting games') }}
        </h3>
      </v-col>
    </v-row>
    <v-row v-if="categories.length > 1">
      <v-col>
        <v-chip-group
          v-model="selectedCategory"
          active-class="primary"
          mandatory
        >
          <v-chip label active @click="filterByCategory('')">
            {{ $t('All') }}
          </v-chip>
          <v-chip v-for="category in categories" :key="category" label @click="filterByCategory(category)">
            {{ category }}
          </v-chip>
        </v-chip-group>
      </v-col>
    </v-row>
    <v-row ref="games" class="justify-center">
      <template v-for="(game, id) in games">
        <v-col
          v-if="!config(id + '.variations')"
          :key="id"
          cols="12"
          md="6"
          lg="3"
          :data-groups="JSON.stringify(config(id + '.categories') || [])"
          class="game-card"
        >
          <game-card :id="id" :name="game.name" :banner="config(id + '.banner')" />
        </v-col>
        <template v-else>
          <v-col
            v-for="variation in config(id + '.variations')"
            :key="variation.slug"
            cols="12"
            md="6"
            lg="3"
            :data-groups="JSON.stringify(variation.categories || [])" class="game-card"
          >
            <game-card :id="id" :name="variation.title" :slug="variation.slug" :banner="variation.banner" />
          </v-col>
        </template>
      </template>
    </v-row>
  </v-container>
</template>

<script>
import { config } from '~/plugins/config'
import Shuffle from 'shufflejs'
import { mapGetters } from 'vuex'
import GameCard from '~/components/GameCard'

export default {
  components: { GameCard },

  data () {
    return {
      selectedCategory: null,
      shuffle: null
    }
  },

  computed: {
    ...mapGetters({
      games: 'package-manager/games'
    }),
    categories () {
      let categories = []

      Object.keys(this.games).forEach(id => {
        const variations = config(id + '.variations')

        if (variations) {
          variations.forEach(variation => {
            categories = categories.concat(variation.categories)
          })
        } else {
          categories = categories.concat(config(id + '.categories'))
        }
      })

      // remove duplicates
      return categories.filter((category, i) => category && categories.indexOf(category) === i)
    }
  },

  methods: {
    config,
    filterByCategory (category) {
      if (!this.shuffle) {
        this.shuffle = new Shuffle(this.$refs.games, { itemSelector: '.game-card' })
      }

      this.shuffle.filter(category)
    }
  }
}
</script>
<style lang="scss" scoped>
.v-chip-group::v-deep {
  .v-slide-group__content {
    justify-content: center;
  }
}
</style>
