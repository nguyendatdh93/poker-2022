<script>
import {config} from '~/plugins/config'
import {route} from '~/plugins/route'
import {mapState} from "vuex";
import Form from "vform";
import axios from "axios";

export default {
  data() {
    return {}
  },
  computed: {
    ...mapState('game-room', ['players']),
  },
  watch: {
    room(room) {
      this.echo.join(`game.${room.id}`)
          .listen('OnPlayersEvent', data => {
              this.$store.dispatch('game-room/setPlayers', JSON.parse(data.players))
          }).listen('GameRoomStartEvent', data => {
              console.log('GameRoomStartEvent', JSON.parse(data.game_room));
              let gameRoom = JSON.parse(data.game_room);
              this.$store.dispatch('game-room/setPlayersBet', gameRoom.players_bet);
              this.$store.dispatch('game-room/setCommunityCard', gameRoom.community_card);
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
