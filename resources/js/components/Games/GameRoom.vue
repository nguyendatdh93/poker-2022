<template>
  <div :class="{ 'd-flex justify-center fill-height align-center': !room }">
    <v-system-bar
        v-if="room"
        color="primary"
        height="35"
        class="playerhandlebar"
    >
      <v-icon>mdi-map-marker</v-icon>
      <span>{{ room.name }}</span>
      <v-icon class="ml-2">mdi-cash</v-icon>
      <span>{{ room.parameters.bet.small }}</span>
      <v-icon class="ml-2">mdi-account-multiple</v-icon>
      <span>{{ $t('{0}/{1}', [playersCount, room.parameters.players_count]) }}</span>
      <v-spacer/>
      <v-btn icon small :disabled="forms.joinOrLeave.busy || playing" @click="leaveRoom">
        <v-icon>mdi-logout-variant</v-icon>
      </v-btn>
    </v-system-bar>
    <template v-else>
      <div class="inner_real" v-if="!room">
        <div class="real_money">
          <div id="title_name">
            <div class="title_name"><h2>Real Money</h2></div>
          </div>
        </div>
        <div class="cashgame">
          <div class="top_arrow"></div>
          <div id="cashgame">
            <h2>Cash Game</h2>
            <div class="tabs_data">
              <ul>
                <li class="active"><a href="#nlhold" id="nlhold-tab" @click="changeTab('nlhold-tab')">NL Hold'em</a></li>
                <li><a href="#lmhold" id="lmhold-tab" @click="changeTab('lmhold-tab')">Limit Hold'em</a></li>
              </ul>
              <div class="tabs_content">
                <div id="nlhold" class="active">
                  <div class="top_text" v-if="stakeSelection.small">
                    <div class="lefttext">Stakes: ${{ stakeSelection.small }}/${{ stakeSelection.big }}</div>
                    <div class="righttxt">Buy-in: ${{ stakeSelection.min}}-${{ stakeSelection.max }}</div>
                  </div>
                  <div class="slider_tab">
                    <select v-model="stakeSelection">
                      <option v-for="(stake, key) in allStakes" :key="key" :value="stake">{{ getStakeValue(stake) }}</option>
                    </select>
                  </div>
                  <div class="btm_text">
                    <h3>Seats per table</h3>
                    <div class="numbr_table">
                      <select v-model="forms.create.players_count">
                        <option value="2">2</option>
                        <option value="6">6</option>
                        <option value="9">9</option>
                      </select>
                    </div>
                  </div>
                  <div class="play_btn" id="gameStartBtn">
                    <button @click="searchRoom()" :disabled="disabledGameStartBtn">Play Now</button>
                  </div>
                </div>

                <div id="lmhold">
                  <div class="top_text" v-if="stakeSelection.small">
                    <div class="lefttext">Stakes: ${{ stakeSelection.small }}/${{ stakeSelection.big }}</div>
                    <div class="righttxt">Buy-in: ${{ stakeSelection.min}}-${{ stakeSelection.max }}</div>
                  </div>
                  <div class="slider_tab">
                    <select v-model="stakeSelection">
                      <option v-for="(stake, key) in allStakes" :key="key" :value="stake">{{ getStakeValue(stake) }}</option>
                    </select>
                  </div>
                  <div class="btm_text">
                    <h3>Seats per table</h3>
                    <div class="numbr_table">
                      <select v-model="forms.create.players_count">
                        <option value="2">2</option>
                        <option value="6">6</option>
                        <option value="9">9</option>
                      </select>
                    </div>
                  </div>
                  <div class="play_btn" id="gameStartBtn">
                    <button @click="searchRoom()" :disabled="disabledGameStartBtn">Play Now</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <block-preloader v-else/>
    </template>
  </div>
</template>

<script>
import {config} from '~/plugins/config'
import axios from 'axios'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import SoundMixin from '~/mixins/Sound'
import FormParameter from '~/components/FormParameter'
import {mapState, mapActions} from 'vuex'
import UserJoinedSound from '~/../audio/common/user-joined.wav'
import UserLeftSound from '~/../audio/common/user-left.wav'
import BlockPreloader from '~/components/BlockPreloader'

export default {
  components: {BlockPreloader, FormParameter},

  mixins: [FormMixin, SoundMixin],

  props: {
    playing: {
      type: Boolean,
      required: true
    }
  },

  data() {
    return {
      disabledGameStartBtn: false,
      room: null,
      rooms: null,
      players: null,
      game: null,
      stakeSelection: {
        small: 1,
        big: 2,
        min: 100,
        max: 200,
      },
      forms: {
        joinOrLeave: new Form({
          room_id: null
        }),
        create: new Form({
          stakes: '1Z/2Z (min 100Z - max 200Z)',
          buy_in: 100,
          games_limit_type: "No Limit Holdem",
          players_count: 2,
        })
      },
      games_limit_type_list: ["No Limit Holdem", "Limit holdem"],
      allStakes: [
          {
            small: 1,
            big: 2,
            min: 100,
            max: 200,
          },
          {
            small: 2,
            big: 5,
            min: 200,
            max: 500,
          },
          {
            small: 5,
            big: 10,
            min: 400,
            max: 1000,
          },
          {
            small: 8,
            big: 16,
            min: 500,
            max: 1500,
          },
          {small: 10, big: 20, min: 500, max: 2500},
          {
            small: 25,
            big: 50,
            min: 1000,
            max: 4000,
          },
          {
            small: 35,
            big: 70,
            min: 1500,
            max: 6000,
          },
          {
            small: 50,
            big: 100,
            min: 2500,
            max: 10000,
          },
          {small: 100, big: 200, min: 4000, max: 15000},
          {
            small: 250,
            big: 500,
            min: 10000,
            max: 20000,
          },
          {small: 500, big: 1000, min: 50000, max: 100000},
          {
            small: 1000,
            big: 2000,
            min: 80000,
            max: 200000,
          },
          {
            small: 2500,
            big: 5000,
            min: 100000,
            max: 500000,
          },
          {
            small: 5000,
            big: 10000,
            min: 300000,
            max: 800000,
          },
          {
            small: 10000,
            big: 20000,
            min: 700000,
            max: 1500000,
          },
      ],
      selectedStakes: [],
      buyInMin: 100,
      buyInMax: 1500000,
    }
  },

  computed: {
    ...mapState('broadcasting', ['echo']),
    ...mapState('auth', ['account', 'user']),

    gamePackageId() {
      return this.$route.params.game
    },
    parameters() {
      return config(`${this.gamePackageId}.parameters`)
    },
    playersCount() {
      return this.players ? this.players.length : 0
    },
    flatStakesList() {
      const values = Object.values(this.allStakes);
      return values.flat();
    }
  },

  watch: {
    room(room, roomBefore) {
      // join a room
      if (room && !roomBefore) {
        this.subscribe(room)
        // leave a room
      } else if (!room && roomBefore) {
        this.unsubscribe(roomBefore)
        this.fetchRooms()
        this.$emit('exit')
      }

      this.$emit('room', {room})
    },
    game(game, gameBefore) {
      if (game && !gameBefore) {
        this.$emit('game', {game})
      }
    },
    playersCount(playersCount) {
      if (playersCount === parseInt(this.room.parameters.players_count, 10)) {
        this.$emit('ready', {ready: true})
      } else {
        this.$emit('ready', {ready: false})
      }
    },
    stakeSelection(stake) {
      this.forms.create.stakes = this.getStakeValue(stake);
    }
  },

  created() {
    this.$nextTick(() => {
      this.fetchRooms()
    })
    this.selectedStakes = this.flatStakesList;
  },

  beforeDestroy() {
    // note that there is no access to this.$route (and hence gamePackageId computed property) in this hook
    this.unsubscribe(this.room)
    this.room = null
    this.rooms = null
    this.players = null
  },

  methods: {
    ...mapActions({
      updateUserAccountBalance: 'auth/updateUserAccountBalance',
    }),
    getStakeValue(stake) {
      return stake.small + "Z/" + stake.big + "Z (min " + stake.min + "Z - max "+ stake.max + "Z)";
    },
    transformStakes() {
      return [...this.selectedStakes.map(stake => `${stake.small}Z/${stake.big}Z (min ${stake.min}Z - max ${stake.max}Z)`)]
    },
    changeTab(tabId) {
      $('.tabs_data ul li').removeClass('active');
      $("#" + tabId).parent().addClass('active');
      $('.tabs_content > div').removeClass('active');
      var idd = $("#" + tabId).attr('href');
      $('.tabs_content').find(idd).addClass('active');
    },
    setBuyInRange(selectedStake) {
      const firstHalf = selectedStake.split('(')[0];
      const foundStake = this.selectedStakes.find(stake => {
        const combineSmallAndBigBlind = `${stake.small}Z/${stake.big}Z`;
        if (firstHalf.trim() == combineSmallAndBigBlind) {
          return stake;
        }
      });
      if (foundStake) {
        this.forms.create.bet = foundStake;
      }

      this.forms.create.buy_in = foundStake.min
    },
    onBuyInChange(buyIn) {

      if (buyIn > this.buyInMax === false || buyIn < this.buyInMin === false) {
        let matchedStake = '';
        [...this.selectedStakes].forEach(stake => {
          if (stake.min <= buyIn && stake.max >= buyIn && !matchedStake) {
            matchedStake = `${stake.small}Z/${stake.big}Z (min ${stake.min}Z - max ${stake.max}Z)`;
          }
        })
        this.forms.create.stakes = matchedStake;
      }
    },

    async fetchRooms() {
      this.disabledGameStartBtn = false;
      const {data} = await axios.get(`/api/games/${this.gamePackageId}/rooms`)

      if (data.room) {
        this.room = data.room
        this.forms.joinOrLeave.room_id = data.room.id

        if (data.game) {
          this.game = data.game;
        }
      }

    },
    async searchRoom() {
        $("#gameStartBtn").css('opacity','0.5');
        this.disabledGameStartBtn = true;
      const {data} = await this.forms.create.post(`/api/games/${this.gamePackageId}/rooms/search`)
      if (data.success) {
        this.forms.joinOrLeave.room_id = data.room.id;
        this.updateUserAccountBalance(this.account.balance - this.forms.create.buy_in);
        await this.joinRoom();
      } else {
        $("#gameStartBtn").css('opacity','0');
        this.disabledGameStartBtn = false;
        this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), {text: data.message})
      }
    },
    async joinRoom() {
      const {data} = await this.forms.joinOrLeave.post(`/api/games/${this.gamePackageId}/rooms/join`)

      if (data.success) {
        this.room = data.room
        this.rooms = null // clear rooms list, so it needs to be fetched again when the player leaves the room
      }
    },
    async leaveRoom() {
      const {data} = await this.forms.joinOrLeave.post(`/api/games/${this.gamePackageId}/rooms/leave`)

      if (data.success) {
        this.room = null
      }
    },
    subscribe(room) {
      if (!this.echo || !room) {
        return false
      }

      this.echo.join(`game.${room.id}`)
          // currently joined players
          .here(players => {
            this.players = players
            this.$emit('players', {players})
          })
          // new player joined
          .joining(player => {
            this.players.push(player)
            this.$emit('player-joined', {player})
            this.sound(UserJoinedSound)
          })
          // player left
          .leaving(player => {
            this.players.splice(this.players.findIndex(item => item.id === player.id), 1)
            this.$emit('player-left', {player})
            this.sound(UserLeftSound)
          })
          // MultiplayerGameStateChanged event
          .listen('MultiplayerGameStateChanged', event => {
            this.$emit('event', {event})
          })
    },
    unsubscribe(room) {
      if (!this.echo || !room) {
        return false
      }

      this.echo.leave(`game.${room.id}`)
    }
  }
}
</script>
<style lang="scss" scoped>
@import '~vuetify/src/styles/settings/_variables';

@media #{map-get($display-breakpoints, 'md-and-up')} {
  .border-left {
    border-left: 1px solid grey;
  }
}

.playerhandlebar{
  box-shadow: inset 0px 0px 30px #e15e1d !important;
  background: #d8aa3a !important;
}
</style>
