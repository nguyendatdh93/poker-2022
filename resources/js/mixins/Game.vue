<script>
import { config } from '~/plugins/config'
import { route } from '~/plugins/route'
import {mapState} from "vuex";
import Form from "vform";
import axios from "axios";

export default {
  data() {
    return {
      round: 1, // 0: init, prelFop: 1, flop: 2, turn: 3, river: 4
      turnForm: new Form({
        message: '',
        recipients: [],
        turn_to_play : 3,
      }),
    }
  },
  computed: {
    ...mapState('broadcasting', ['echo']),
    gamePackageId () {
      return this.$route.params.game
    },
    gameSlug () {
      return this.$route.params.slug
    },
    config () {
      return this.gameSlug
        ? config(`${this.gamePackageId}`).variations.find(o => o.slug === this.gameSlug)
        : config(`${this.gamePackageId}`)
    },
    provablyFairGame () {
      return this.$store.getters['provably-fair/get'](this.gamePackageId) || {}
    }
  },

  methods: {
    getRoute (action) {
      return route(`games.${this.gamePackageId}.${action}`)
    },
    nextTurn() {
      this.turnForm = new Form({
        message: `${this.user.name} it's your turn. You have 30 seconds to act`,
        recipients: [],
        turn_to_play: this.user.id,
      });

      this.turnForm.post(`/api/chat/${this.room.id}`)
      this.turnForm.message = ''
      this.turnForm.recipients = []
    },
    showActions() {
      if (!this.players[this.turnForm.turn_to_play]) {
          return false;
      }

      if (this.players[this.turnForm.turn_to_play].id != this.user.id) {
        return false;
      }

      return true;
    },
  }
}
</script>
