<template>
  <div id="dealer_button_custom" class="d_card_0">
    <img src="/v2/images/deal-btn.png" style="width: 100%" alt=""/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "DealerIcon",
  props: ['room-prop'],
  data() {
    return {
      room: null,
    }
  },
  created() {
    this.room = this.roomProp;
  },
  watch: {
    room: function (val) {
      this.distributeDealerIcon();
    }
  },
  computed: {
    ...mapState('broadcasting', ['echo']),
  },
  methods: {
    distributeDealerIcon() {
      setTimeout(function () {
        $('#dealer_button_custom').removeAttr('class');
        $('#dealer_button_custom').addClass($('.is-dealer')[0].classList[0] + ' ' + $('.is-dealer')[0].classList[1] + ' ' + $('.is-dealer')[0].classList[3]);
        $('#dealer_button_custom').css('transition', '1s all');
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
  height: 0px;
  overflow: visible;
  margin-left: -25px !important;
  margin-top: -30px !important;
}
</style>