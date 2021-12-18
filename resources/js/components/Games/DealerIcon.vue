<template>
  <div id="dealer_button_custom" class="d_card_0">
    <img src="/v2/images/deal-btn.png" alt=""/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "DealerIcon",
  props: ['room'],
  created() {
    this.echo.join(`game.${this.room.id}`).listen('GameRoomStartEvent', data => {
      this.distributeDealerIcon();
    });
  },
  computed: {
    ...mapState('broadcasting', ['echo']),
  },
  methods: {
    distributeDealerIcon() {
      setTimeout(function () {
        $('#dealer_button_custom').removeAttr('class');
        $('#dealer_button_custom').addClass($('.is-dealer')[0].classList[3]);
        $('#dealer_button_custom').css('transition', '.5s all');
      }, 2000);
    },
  }
}
</script>

<style scoped>
#dealer_button_custom::before {
  display: none !important;
}

#dealer_button_custom {
  position: absolute;
  width: 30px;
  height: 30px;
  overflow: visible;
  margin-left: -25px !important;
  margin-top: -30px !important;
}
</style>