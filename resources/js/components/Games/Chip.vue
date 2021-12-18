<template>
  <div :class="`poker_icon left chip-` + opponent.user_id" v-if="chips">
    <img src="/v2/images/pokr-icon.png" alt="">
    <span>{{ getChip() }}</span>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "Chip",
  props: ['opponent'],
  computed: {
    ...mapState('game-room', ['chips']),
  },
  methods: {
    getChip() {
      if (this.chips) {
        for (let i=0; i<= this.chips.length; i++) {
          if (this.chips[i] && this.chips[i].user_id == this.opponent.user_id) {
            return this.chips[i].chip;
          }
        }
      }

      return 0;
    }
  }
}
</script>

<style scoped>

</style>