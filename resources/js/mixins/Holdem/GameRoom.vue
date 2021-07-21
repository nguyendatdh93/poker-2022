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
              this.$store.dispatch('game-room/fetchCommunityCard', {
                hash: this.provablyFairGame.hash,
                room_id: gameRoom.id
              });
              this.$store.dispatch('game-room/action', {
                hash: this.provablyFairGame.hash,
                room_id: gameRoom.id
              });
          }).listen('GameRoomCommunityCardEvent', data => {
              console.log('GameRoomCommunityCardEvent', data);
              this.$store.dispatch('game-room/setCommunityCard', data.community_card);
          }).listen('ActionEvent', data => {
              console.log('ActionEvent', data);
              this.$store.dispatch('game-room/setAction', data.player);
          }).listen('CallEvent', data => {
              console.log('CallEvent', data);
              this.updateUserAccountBalance(data.account.balance);
              this.$store.dispatch('game-room/setPlayersBet', data.players_bet);
              this.$store.dispatch('game-room/fetchCommunityCard', {
                hash: this.provablyFairGame.hash,
                room_id: room.id
              });
              this.$store.dispatch('game-room/action', {
                hash: this.provablyFairGame.hash,
                room_id: room.id
              });
          }).listen('RaiseEvent', data => {
              console.log('RaiseEvent', data);
              this.updateUserAccountBalance(data.account.balance)
              this.$store.dispatch('game-room/setPlayersBet', data.players_bet);
              this.$store.dispatch('game-room/action', {
                hash: this.provablyFairGame.hash,
                room_id: room.id
              });
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
  }
}
</script>
