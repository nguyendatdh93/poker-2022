<template>
  <div class="hand" :class="{ inactive: inactive, player: player }" :id="id">
    <slot name="title">
      <div
        v-if="title"
        class="font-weight-thin title text-center mb-2 ml-n10 ml-lg-0"
      >
        {{ title }}
      </div>
    </slot>
    <slot />
    <div class="playing-cards">
      <transition-group name="deal" tag="div" class="d-flex justify-center">
        <playing-card
          v-for="(card, i) in cards"
          :key="`card-${i}`"
          :card="card"
          :clickable="clickable"
          :inactive="inactiveCards.indexOf(card) > -1"
          @click.native="click(i, card)"
          :is-player-card="true"
        >
          <template v-slot:top>
            <slot v-if="$scopedSlots['top.' + i]" :name="`top.${i}`" />
          </template>
        </playing-card>
      </transition-group>
      <hand-score :score="score" />
      <hand-result :result="result" :class="resultClass" />
    </div>
    <slot name="bottom"></slot>
    <hand-bet-win :bet="bet" :win="win" />
  </div>
</template>

<script>
import PlayingCard from "./PlayingCard";
import HandScore from "./HandScore";
import HandResult from "./HandResult";
import HandBetWin from "./HandBetWin";

export default {
  components: { PlayingCard, HandScore, HandResult, HandBetWin },
  props: {
    // array of cards, e.g. ['H2', 'H3']
    cards: {
      type: Array,
      required: true,
    },
    score: {
      type: Number,
      required: false,
      default: -1,
    },
    result: {
      type: String,
      required: false,
      default: "",
    },
    resultClass: {
      type: String,
      required: false,
      default: "",
    },
    bet: {
      type: Number,
      required: false,
      default: 0,
    },
    win: {
      type: Number,
      required: false,
      default: 0,
    },
    inactive: {
      type: Boolean,
      required: false,
      default: false,
    },
    player: {
      type: Boolean,
      required: false,
      default: false,
    },
    id: {
      type: String,
      required: false,
      default: "",
    },
    inactiveCards: {
      type: Array,
      required: false,
      default: () => [],
    },
    title: {
      type: String,
      required: false,
      default: "",
    },
    clickable: {
      type: Boolean,
      required: false,
      default: false,
    },
  },

  methods: {
    click(index, card) {
      this.$emit("playing-card-click", { index, card });
    },
  },
};
</script>

<style lang="scss" scoped>
.hand {
  min-height: 7em;
  transition: all 0.5s ease;

  &.inactive {
    opacity: 0.4;
  }

  .playing-cards {
    top:-92px;
    position: relative;
  }
}
#opponent_1 {
    position: absolute;
    top: 5%;
}
#opponent_2 {
  position: absolute;
  top: 40%;
  left: 7%;
}
#opponent__2 {
  position: absolute;
  left: 38%;
  top: 7%;
}
#opponent_3 {
  position: absolute;
  top: 40%;
  right: 7%;
}
#opponent_4 {
  position: absolute;
  top: 5%;
  left: 18%;
}
#opponent_5 {
   position: absolute;
  top: 5%;
  right: 18%;
}
@media screen and (max-width:1900px){
#opponent_1 {
    position: absolute;
    top: 4%;
}
#opponent_2 {
    position: absolute;
    top: 40%;
    left: 10%;
}
#opponent_3 {
    position: absolute;
    top: 40%;
    right: 12%;
}
#opponent_4 {
    position: absolute;
    top: 15%;
    left: 18%;
}
#opponent_5 {
    position: absolute;
    top: 15%;
    display: block;
    right: 18%;
}
div#opponent_6 {
    display: block !important;
    top: 63%;
    left: 19%;
    position: absolute;
}
div#opponent_7 {
    display: block;
    position: absolute;
    top: 63%;
    right: 16.5%;
}
div#opponent_8 {
    display: block;
    position: absolute;
    bottom: 6%;
    right: 33%;
}
#primary_user {
    position: absolute;
    bottom: 6%;
    left: 45%;
}

}
@media screen and (max-width:1262px){
.playing-card-container {
    margin: 0px 15px !important;
}

}
@media screen and (max-width:768px){
#opponent_1 {
    position: absolute;
    top: 15%;
}
#primary_user {
    position: absolute;
    bottom: 13%;
    left: 45%;
}
div#opponent_8 {
    display: block;
    position: absolute;
    bottom: 13%;
    right: 33%;
}
}
#primary_user{
 position: absolute;
  bottom: 3%;
}

.deal-enter-active {
  animation: deal 0.3s;
}

.deal-leave-active {
  animation: deal 0.3s reverse;
}

.deal-move {
  transition: transform 0.3s;
}
.player {
  position: absolute;
  bottom: 60px;
}

@keyframes deal {
  0% {
    transform: translateY(-100vh);
  }
  100% {
    transform: translateY(0);
  }
}
</style>
