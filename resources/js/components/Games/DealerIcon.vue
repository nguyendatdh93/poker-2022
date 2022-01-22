<template>
  <div id="dealer_button_custom" class="cards ani0 d_card_0">
    <img src="/v2/images/deal-btn.png" style="width: 100%" alt=""/>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "DealerIcon",
  props: ['room-prop', 'dealer'],
  data() {
    return {
      room: null,
      dealerID: null,
    }
  },
  computed: {
    ...mapState('broadcasting', ['echo']),
  },
  created() {
    this.room = this.roomProp;
    this.distributeDealerIcon();
  },
  watch: {
    dealer: function (val) {
      this.distributeDealerIcon();
    }
  },
  methods: {
    distributeDealerIcon() {
      setTimeout(function () {
        $('#dealer_button_custom').removeAttr('class');
        $('#dealer_button_custom').css('transition', '1s all');
        $('#dealer_button_custom').addClass($('.is-dealer')[0].classList[0] + ' ' + $('.is-dealer')[0].classList[1] + ' ' + $('.is-dealer')[0].classList[3]);
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