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
    position: relative;
  }
}
#opponent_2 {
  position: absolute;

}
#opponent__2 {
position: absolute;
    left: 38%;
    top: 10%;

}
#opponent_3 {
  position: absolute;
    top: 40%;
  left: 20%;
}
#opponent_4 {
  position: absolute;
      top: 40%;
    right: 20%;
}
#opponent_5 {
  position: absolute;
    top: 63%;
    left: 24%;
}
#opponent_6 {
 position: absolute;
    top: 12%;
    left: 25%;
}
#opponent_7 {
  position: absolute;
    top: 12%;
    right: 25%;
}
#opponent_8 {
 position: absolute;
    bottom: 15%;
    right: 25%;
}
#opponent__9 {
    position: absolute;
    left: 52%;
    top: 10%;
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
