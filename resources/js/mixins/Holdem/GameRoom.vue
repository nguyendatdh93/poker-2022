<script>
import { config } from '~/plugins/config'
import { route } from '~/plugins/route'
import {mapState} from "vuex";
import Form from "vform";
import axios from "axios";

export default {
  data() {
    return {
    }
  },
  computed: {
    ...mapState('game-room', ['players']),
  },
  watch: {
    room(room) {
      this.echo.join(`game.${room.id}`)
          .listen('OnPlayersEvent', data => {
            this.$store.dispatch('game-room/setPlayers', JSON.parse(data.players))
          });
    },
  },
  methods: {
    updatePlayerHand(player, values) {
      Object.keys(values).forEach(key => {
        player[key] = values[key]
      })
    },
  }
}
</script>
