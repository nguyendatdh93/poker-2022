<template>
  <v-select
    v-model="value"
    :items="options"
    :label="$t('Game')"
    :disabled="disabled"
    outlined
    @change="change"
  />
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  props: {
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      value: null
    }
  },

  computed: {
    ...mapGetters({
      games: 'package-manager/games'
    }),
    options () {
      return [
        { text: this.$t('All'), value: null },
        ...Object.keys(this.games).map(id => ({ text: this.games[id].name, value: id }))
      ]
    }
  },

  methods: {
    change () {
      this.$emit('change', { game: this.value })
    }
  }
}
</script>
