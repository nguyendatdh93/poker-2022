<template>
  <v-container class="mt-10">
    <v-row>
      <v-col class="text-center">
        <h3 class="display-1 font-weight-thin">
          {{ $t('Earn free credits and bonuses') }}
        </h3>
      </v-col>
    </v-row>
    <v-row class="justify-center">
      <v-col cols="12" md="6" lg="3" class="text-center">
        <v-card elevation="6" outlined shaped style="min-height: 220px">
          <div class="mt-5">
            <v-avatar color="primary" size="60">
              <v-icon color="white" large>
                mdi-account-plus
              </v-icon>
            </v-avatar>
          </div>
          <v-card-title class="d-block">
            {{ $t('Sign up bonus') }}
          </v-card-title>

          <v-card-text>
            <p>
              {{ $t('Sign up and get {0} free credits to play.', [config('settings.bonuses.sign_up')] ) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col v-if="paymentsPackageEnabled" cols="12" md="6" lg="3" class="text-center">
        <v-card elevation="6" outlined shaped style="min-height: 220px">
          <div class="mt-5">
            <v-avatar color="primary" size="60">
              <v-icon color="white" large>
                mdi-cash-plus
              </v-icon>
            </v-avatar>
          </div>
          <v-card-title class="d-block">
            {{ $t('Deposit bonus') }}
          </v-card-title>

          <v-card-text>
            <p>
              {{ $t('Get {0}% on top when you deposit {1} credits or more.', [config('settings.bonuses.deposit.pct'), config('settings.bonuses.deposit.threshold')] ) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="6" lg="3" class="text-center">
        <v-card elevation="6" outlined shaped style="min-height: 220px">
          <div class="mt-5">
            <v-avatar color="primary" size="60">
              <v-icon color="white" large>
                mdi-link
              </v-icon>
            </v-avatar>
          </div>
          <v-card-title class="d-block">
            {{ $t('Affiliate program') }}
          </v-card-title>

          <v-card-text>
            <p>
              {{ $t('Refer your friends to the casino and get bonuses when they play games.') }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { config } from '~/plugins/config'

export default {
  computed: {
    paymentsPackageEnabled () {
      return this.$store.getters['package-manager/get']('payments')
    }
  },

  methods: {
    config
  }
}
</script>
