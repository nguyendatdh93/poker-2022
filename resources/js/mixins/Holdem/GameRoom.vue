<script>
import {config} from '~/plugins/config'
import {route} from '~/plugins/route'
import {mapActions, mapState} from "vuex";
import Form from "vform";
import axios from "axios";

export default {
  data() {
    return {}
  },
  computed: {
    ...mapState('game-room', ['players', 'gameRoom']),
  },
  watch: {
    room(room) {
      this.echo.join(`game.${room.id}`)
          .listen('OnPlayersEvent', data => {
            this.$store.dispatch('game-room/setPlayers', JSON.parse(data.players))
          }).listen('GameRoomStartEvent', data => {
              console.log('GameRoomStartEvent', data);
              this.$store.dispatch('game-room/setGameRoom', data.game_room);
          }).listen('GameRoomPlayEvent', data => {
            let gameRoom = JSON.parse(data.game_room);
            console.log('GameRoomPlayEvent', gameRoom);
            this.$store.dispatch('game-room/setGameRoom', gameRoom);
          });
    },
  },
  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
    }),
    updatePlayerHand(player, values) {
      Object.keys(values).forEach(key => {
        player[key] = values[key]
      })
    },
    getPlayerActionIndex(playerId) {
      if (this.gameRoom.players) {
        let players = Object.values(this.gameRoom.players);
        console.log('this.gameRoom.players', players);
        return players.findIndex((player) => {
          return player == playerId;
        })
      }

      return -1;
    }
  }
}
</script>
