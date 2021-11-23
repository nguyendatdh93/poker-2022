<template>
  <div class="table-text">
    <div class="table-text1">
      <div id="table-text">
        <div class="pot" v-if="gameRoom.pot > 0">
          <span>Pot: {{ gameRoom.pot }}</span>
        </div>
        <div class="righttext">
          <ul>
            <li>Real Money Table</li>
            <li>Bussolini III - No Limit Hold'em</li>
            <li>50/100 Play Money</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "HoldemTableText",
  computed: {
    ...mapState('game-room', ['gameRoom']),
  }
}
</script>

<style scoped>
#table-text {
  bottom: unset !important;
}
</style>