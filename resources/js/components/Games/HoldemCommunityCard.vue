<template>
  <div id="community-card" class="d-flex justify-center mt-2">
    <div v-for="(card, i) in gameRoom.community_card" :key="`card-${i}`" :class="`c_cards ` + `c_card_${i}`">
      <playing-card
          :card="card"
          :clickable="false"
          :is-player-card="false"
          @loadedCard="showCommunityCard(i)"
      >
        <template v-slot:top>
          <slot v-if="$scopedSlots['top.' + i]" :name="`top.${i}`"/>
        </template>
      </playing-card>
    </div>
  </div>
</template>

<script>
import {mapState} from "vuex";
import PlayingCard from "./Cards/PlayingCard";

export default {
  name: "HoldemCommunityCard",
  components: { PlayingCard },
  computed: {
    ...mapState('game-room', ['gameRoom']),
  },
  watch: {
    'gameRoom.community_card' : function () {
      // this.showCommunityCard();
    }
  },
  methods: {
    showCommunityCard(i) {
        setTimeout(function () {
          console.log($("c_card_"+i))
          $("c_card_"+i).css('display', 'block');
          $(".c_card_"+i).css('left', '0px');
          $(".c_card_"+i).css('position', 'relative');
          $(".c_card_"+i).css('top', '0px');
          $(".c_card_"+i).css('transition', 'all 1s cubic-bezier(0.68, -0.55, 0.265, 1.55)');
          $(".c_card_"+i).css('transform', 'scale(1)');
        }, i * 500);
    }
  }
}
</script>

<style scoped>
  .c_cards {
    position: absolute;
    top: -400px;
    left: -200px;
    transform: scale(0);
    transition: all 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }
</style>