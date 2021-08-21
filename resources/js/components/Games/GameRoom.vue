<template>
  <div :class="{ 'd-flex justify-center fill-height align-center': !room }">
    <v-system-bar
      v-if="room"
      color="primary"
      height="35"
    >
      <v-icon>mdi-map-marker</v-icon>
      <span>{{ room.name }}</span>
      <v-icon class="ml-2">mdi-cash</v-icon>
      <span>{{ room.parameters.bet.small }}</span>
      <v-icon class="ml-2">mdi-account-multiple</v-icon>
      <span>{{ $t('{0}/{1}', [playersCount, room.parameters.players_count]) }}</span>
      <v-spacer />
      <v-btn icon small :disabled="forms.joinOrLeave.busy || playing" @click="leaveRoom">
        <v-icon>mdi-logout-variant</v-icon>
      </v-btn>
    </v-system-bar>
    <template v-else>
      <v-container v-if="!room" fluid class="align-self-start">
        <v-row align="center" justify="center">
          <v-col cols="12" md="8">
            <v-card>
              <v-toolbar>
                <v-toolbar-title>
                  {{ $t('Join Game rooms') }}
                </v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-row>
                  <v-col cols="12" class="pr-md-5">
                    <template v-if="!room">
                       
                <v-form @submit.prevent="searchRoom">
                  <v-select
                    v-model="forms.create.games_limit_type"
                    :items="games_limit_type_list"
                    label="Select Game"
                    auto-select-first
                    outlined
                  
                  ></v-select>

                  <v-select
                    v-model="forms.create.stakes"
                    :items="transformStakes()"
                    label="Select stakes"
                    outlined
                    @change="setBuyInRange(forms.create.stakes)"
                  ></v-select>
                  <v-text-field
                    v-model="forms.create.buy_in"
                    :label="$t('Buy-In')"
                    :rules="[
                      validationRequired,
                      v  =>  buyInMin <= Number(v) || 'buy in should greater than '+ buyInMin,
                      v  =>  buyInMax >= Number(v) || 'buy in should less than '+ buyInMax
                    ]"
                    :error="forms.create.errors.has('buy_in')"
                    :error-messages="forms.create.errors.get('buy_in')"
                    outlined
                    :disabled="forms.create.busy"
                    @keydown="clearFormErrors($event, 'buy_in', forms.create)"
                    @keyup="onBuyInChange(forms.create.buy_in)"
                  />
                   <v-text-field
                    v-model="forms.create.players_count"
                    :label="$t('Maximum players')"
                    :rules="[
                      validationRequired,
                      v  => Number(v) > 1 || 'buy in should greater than'+ 1,
                      v  => Number(v) < 10 || 'buy in should less than'+ 10
                    ]"
                    :error="forms.create.errors.has('players_count')"
                    :error-messages="forms.create.errors.get('players_count')"
                    outlined
                    :disabled="forms.create.busy"
                    @keydown="clearFormErrors($event, 'players_count', forms.create)"
                  />
           
                        <v-btn type="submit" color="primary" :disabled="forms.joinOrLeave.busy" :loading="forms.joinOrLeave.busy">
                          {{ $t('Join') }}
                        </v-btn>
                      </v-form>
                    </template>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
      <block-preloader v-else />
    </template>
  </div>
</template>

<script>
import { config } from '~/plugins/config'
import axios from 'axios'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import SoundMixin from '~/mixins/Sound'
import FormParameter from '~/components/FormParameter'
import { mapState } from 'vuex'
import UserJoinedSound from '~/../audio/common/user-joined.wav'
import UserLeftSound from '~/../audio/common/user-left.wav'
import BlockPreloader from '~/components/BlockPreloader'

export default {
  components: { BlockPreloader, FormParameter },

  mixins: [FormMixin, SoundMixin],

  props: {
    playing: {
      type: Boolean,
      required: true
    }
  },

  data () {
    return {
      room: null,
      rooms: null,
      players: null,
      game: null,
      forms: {
        joinOrLeave: new Form({
          room_id: null
        }),
        create: new Form({
          stakes: `${1}Z/${2}Z (min ${100}Z - max ${200}Z)`,
          buy_in: 100,
          games_limit_type: "No Limit Holdem",
          players_count:2,
        })
      },
      games_limit_type_list: ["No Limit Holdem", "Limit holdem"],
      allStakes: {
        micro: [
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
        ],

        low: [
          { small: 10, big: 20, min: 500, max: 2500 },
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
        ],
        medium: [
          {
            small: 50,
            big: 100,
            min: 2500,
            max: 10000,
          },
          { small: 100, big: 200, min: 4000, max: 15000 },
          {
            small: 250,
            big: 500,
            min: 10000,
            max: 20000,
          },
        ],
        high: [
          { small: 500, big: 1000, min: 50000, max: 100000 },
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
      },
     selectedStakes:[],
     buyInMin:100,
     buyInMax:1500000,
    }
  },

  computed: {
    ...mapState('broadcasting', ['echo']),
    gamePackageId () {
      return this.$route.params.game
    },
    parameters () {
      return config(`${this.gamePackageId}.parameters`)
    },
    playersCount () {
      return this.players ? this.players.length : 0
    },
    flatStakesList(){
      const values = Object.values(this.allStakes);
      return values.flat();
    }
  },

  watch: {
    room (room, roomBefore) {
      // join a room
      if (room && !roomBefore) {
        this.subscribe(room)
      // leave a room
      } else if (!room && roomBefore) {
        this.unsubscribe(roomBefore)
        this.fetchRooms()
        this.$emit('exit')
      }

      this.$emit('room', { room })
    },
    game (game, gameBefore) {
      if (game && !gameBefore) {
        this.$emit('game', { game })
      }
    },
    playersCount (playersCount) {
      if (playersCount === parseInt(this.room.parameters.players_count, 10)) {
        this.$emit('ready', { ready: true })
      } else {
        this.$emit('ready', { ready: false })
      }
    }
  },

  created () {
    // it's important to wait for next tick,
    // because the game component can be initialized from beforeRouteUpdate() hook,
    // when the route parameters are not het updated
    this.$nextTick(() => {
      this.fetchRooms()

      if (this.parameters) {
        this.parameters.forEach(parameter => {
          this.forms.create.parameters[parameter.id] = parameter.default
        })
      }
    })
  this.selectedStakes = this.flatStakesList;

  },

  beforeDestroy () {
    // note that there is no access to this.$route (and hence gamePackageId computed property) in this hook
    this.unsubscribe(this.room)
    this.room = null
    this.rooms = null
    this.players = null
  },

  methods: {
    transformStakes(){
      console.log(this.selectedStakes);
        return [...this.selectedStakes.map(stake=>`${stake.small}Z/${stake.big}Z (min ${stake.min}Z - max ${stake.max}Z)`)]
    },
    setBuyInRange(selectedStake){
        const firstHalf = selectedStake.split('(')[0];
        const foundStake = this.selectedStakes.find(stake=>{
           const combineSmallAndBigBlind = `${stake.small}Z/${stake.big}Z`;
           if(firstHalf.trim()==combineSmallAndBigBlind){
               return stake;
           }
        });
        if(foundStake){
            this.forms.create.bet = foundStake;
        }

          this.forms.create.buy_in = foundStake.min
    },
    onBuyInChange(buyIn){

      if(buyIn > this.buyInMax === false || buyIn < this.buyInMin === false){
        let matchedStake='';
      [...this.selectedStakes].forEach(stake=>
      {
        if(stake.min <= buyIn && stake.max >=buyIn && !matchedStake){
       matchedStake = `${stake.small}Z/${stake.big}Z (min ${stake.min}Z - max ${stake.max}Z)`;
        }
      })
      this.forms.create.stakes=matchedStake;
      }
    },

    async fetchRooms () {
      const { data } = await axios.get(`/api/games/${this.gamePackageId}/rooms`)

      if (data.room) {
        this.room = data.room
        this.forms.joinOrLeave.room_id = data.room.id

        if (data.game) {
          this.game = data.game;
        }
      }

    },
    async searchRoom () {
      const { data } = await this.forms.create.post(`/api/games/${this.gamePackageId}/rooms/search`)
      if (data.success) {
        this.forms.joinOrLeave.room_id = data.room.id
        await this.joinRoom();
      }else{
       this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })
      }
    },
    async joinRoom () {
      const { data } = await this.forms.joinOrLeave.post(`/api/games/${this.gamePackageId}/rooms/join`)

      if (data.success) {
        this.room = data.room
        this.rooms = null // clear rooms list, so it needs to be fetched again when the player leaves the room
      }
    },
    async leaveRoom () {
      const { data } = await this.forms.joinOrLeave.post(`/api/games/${this.gamePackageId}/rooms/leave`)

      if (data.success) {
        this.room = null
      }
    },
    subscribe (room) {
      if (!this.echo || !room) {
        return false
      }

      this.echo.join(`game.${room.id}`)
      // currently joined players
        .here(players => {
          this.players = players
          this.$emit('players', { players })
        })
        // new player joined
        .joining(player => {
          this.players.push(player)
          this.$emit('player-joined', { player })
          this.sound(UserJoinedSound)
        })
        // player left
        .leaving(player => {
          this.players.splice(this.players.findIndex(item => item.id === player.id), 1)
          this.$emit('player-left', { player })
          this.sound(UserLeftSound)
        })
      // MultiplayerGameStateChanged event
      .listen('MultiplayerGameStateChanged', event => {
        this.$emit('event', { event })
      })
    },
    unsubscribe (room) {
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
</style>
