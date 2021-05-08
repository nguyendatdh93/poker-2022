<template>
  <div>
    <dynamic-template
      :template="html"
      :components="{ Slider, GamesList, ProvablyFair, BonusCards, Raffles, RecentGames }"
    />
  </div>
</template>
<script>
import axios from 'axios'
import DynamicTemplate from '~/components/DynamicTemplate'
import Slider from '~/components/Home/Slider'
import GamesList from '~/components/Home/GamesList'
import ProvablyFair from '~/components/Home/ProvablyFair'
import BonusCards from '~/components/Home/BonusCards'
import Raffles from '~/components/Home/Raffles'
import RecentGames from '~/components/Home/RecentGames'

export default {
  components: { DynamicTemplate },

  metaInfo () {
    return {
      title: this.$t('Fair online casino games')
    }
  },

  data () {
    return {
      Slider,
      GamesList,
      ProvablyFair,
      BonusCards,
      Raffles,
      RecentGames,
      html: ''
    }
  },

  async created () {
    const { data: { html } } = await axios.get('/api/pages/home')
    this.html = `<div>${html}</div>`
  }
}
</script>
