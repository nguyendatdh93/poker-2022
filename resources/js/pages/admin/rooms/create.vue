<template>
  <v-container fluid class="align-self-start">
    <v-row align="center" justify="center">
      <v-col cols="12" md="8">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t("Create a new room") }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" class="pl-md-5 border-left">
                <v-form @submit.prevent="createRoom">
                  <v-text-field
                    v-model="forms.create.name"
                    :label="$t('Room name')"
                    :rules="[
                      validationRequired,
                      (v) => validationMinLength(v, 3),
                      (v) => validationMaxLength(v, 50),
                    ]"
                    :error="forms.create.errors.has('name')"
                    :error-messages="forms.create.errors.get('name')"
                    outlined
                    :disabled="forms.create.busy"
                    @keydown="clearFormErrors($event, 'name', forms.create)"
                  />
                  <v-combobox
                    v-model="forms.create.games_type"
                    :items="games_type"
                    label="Select Game type"
                    outlined
                    @change="getStakeFromGameType(forms.create.games_type)"
                  ></v-combobox>

                  <v-combobox
                    v-model="forms.create.stakes"
                    :items="transformStakes()"
                    label="Select stakes"
                    outlined
                    @change="setBuyInRange(forms.create.stakes)"
                  ></v-combobox>
              
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
                  <v-btn
                    type="submit"
                    color="primary"
                    :disabled="!forms.create.name || forms.create.busy"
                    :loading="forms.create.busy"
                  >
                    {{ $t("Create") }}
                  </v-btn>
                </v-form>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { config } from "~/plugins/config";
import axios from "axios";
import Form from "vform";
import FormMixin from "~/mixins/Form";
import FormParameter from "~/components/FormParameter";
import { mapState } from "vuex";
import BlockPreloader from "~/components/BlockPreloader";

export default {
  components: { BlockPreloader, FormParameter },

  mixins: [FormMixin],

  data() {
    return {
      games_type: ["Micro", "Low", "Medium", "High"],
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
      forms: {
        create: new Form({
          name: null,
          stakes: null,
          games_type: null,
          players_count:2,
          bet:null
        }),
      },
    };
  },

  created() {},

  methods: {
    async createRoom() {
      const { data } = await this.forms.create.post(
        `/api/games/casino-holdem/rooms`
      );
       this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

      this.$router.push({ name: 'admin.rooms.index' })
    },
    
    getStakeFromGameType(gameType){
        gameType = gameType.toLowerCase();
        this.selectedStakes = this.allStakes[gameType];
    },
    transformStakes(){
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
    },
  },
};
</script>
<style lang="scss" scoped>
@import "~vuetify/src/styles/settings/_variables";
</style>
