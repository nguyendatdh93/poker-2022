<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="6">
        <v-card :loading="!profit">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('All-time profit') }}
              ({{ $t('credits') }})
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col>
                <v-skeleton-loader type="list-item" :loading="!profit">
                  <div class="headline text-center">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <span v-bind="attrs" v-on="on">{{ integer(profit.deposits_total) }}</span>
                      </template>
                      <span>{{ $t('Total amount of deposits (only completed)') }}</span>
                    </v-tooltip>
                    <v-icon class="text--secondary">mdi-minus</v-icon>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <span v-bind="attrs" v-on="on">{{ integer(profit.withdrawals_total) }}</span>
                      </template>
                      <span>{{ $t('Total amount of withdrawals (except cancelled)') }}</span>
                    </v-tooltip>
                    <v-icon class="text--secondary">mdi-minus</v-icon>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <span v-bind="attrs" v-on="on">{{ integer(profit.accounts_total) }}</span>
                      </template>
                      <span>{{ $t('Outstanding user balances (except admins and bots)') }}</span>
                    </v-tooltip>
                  </div>
                </v-skeleton-loader>
                <v-skeleton-loader type="list-item-two-line" :loading="!profit">
                  <div class="display-2 text-center my-5">
                    <v-icon v-if="totalProfit > 0" x-large class="green--text">
                      mdi-chevron-double-up
                    </v-icon>
                    <v-icon v-if="totalProfit < 0" x-large class="red--text">
                      mdi-chevron-double-down
                    </v-icon>
                    <span :class="{ 'green--text': totalProfit > 0, 'red--text': totalProfit < 0 }">
                      {{ integer(totalProfit) }}
                    </span>
                  </div>
                </v-skeleton-loader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" lg="6" class="text-center">
        <v-card :loading="!balance">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Balance') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['period']"
              :disabled="!balance"
              @apply="loadData('accounting-balance', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="depositsTotal > 0 || withdrawalsTotal > 0 ? 100 * depositsTotal / (depositsTotal + withdrawalsTotal) : 0"
                  :color="depositsTotal > withdrawalsTotal ? 'primary' : 'error'"
                >
                  <span class="headline">{{ short(depositsTotal) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('deposits') }}
                </v-subheader>
              </v-col>
              <v-col cols="12" md="6" class="text-center">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="depositsTotal > 0 || withdrawalsTotal > 0 ? 100 * withdrawalsTotal / (depositsTotal + withdrawalsTotal) : 0"
                  :color="depositsTotal > withdrawalsTotal ? 'primary' : 'error'"
                >
                  <span class="headline">{{ short(withdrawalsTotal) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('withdrawals') }}
                </v-subheader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" lg="6" class="text-center">
        <v-card :loading="!deposits">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Deposits') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['comparison-period']"
              :disabled="!deposits"
              @apply="loadData('accounting-deposits-comparison', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="previousDeposits > 0 || currentDeposits > 0 ? 100 * previousDeposits / (previousDeposits + currentDeposits) : 0"
                  color="primary"
                >
                  <span class="headline">{{ short(previousDeposits) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('previous') }}
                </v-subheader>
              </v-col>
              <v-col cols="12" md="6" class="text-center">
                <v-progress-circular
                  :rotate="-90"
                  :size="200"
                  :width="30"
                  :value="previousDeposits > 0 || currentDeposits > 0 ? 100 * currentDeposits / (previousDeposits + currentDeposits) : 0"
                  color="primary"
                >
                  <span class="headline">{{ short(currentDeposits) }}</span>
                </v-progress-circular>
                <v-subheader class="title font-weight-thin justify-center mt-3">
                  {{ $t('current') }}
                </v-subheader>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" lg="6">
        <v-card class="text-center" :loading="!data['accounting-deposits-history']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Deposits last 8 weeks') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :value="data['accounting-deposits-history'] || Array(8).fill(0)"
                :color="chartLineColor"
                height="150"
                padding="24"
                stroke-linecap="round"
                line-width="2"
                smooth="5"
                auto-draw
                :auto-draw-duration="2000"
              >
                <template v-slot:label="item">
                  {{ short(item.value) }}
                </template>
              </v-sparkline>
            </v-sheet>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" lg="6">
        <v-card class="text-center" :loading="!data['accounting-withdrawals-history']">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Withdrawals last 8 weeks') }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-sheet>
              <v-sparkline
                :value="data['accounting-withdrawals-history'] || Array(8).fill(0)"
                :color="chartLineColor"
                height="150"
                padding="24"
                stroke-linecap="round"
                line-width="2"
                smooth="5"
                auto-draw
                :auto-draw-duration="2000"
              >
                <template v-slot:label="item">
                  {{ short(item.value) }}
                </template>
              </v-sparkline>
            </v-sheet>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" lg="12">
        <v-card class="text-center" :loading="!depositsByStatus">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Deposits by status') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['period']"
              :disabled="!depositsByStatus"
              @apply="loadData('accounting-deposits-by-status', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t('Status') }}</th>
                    <th class="text-right">{{ $t('Count') }}</th>
                    <th class="text-right">{{ $t('Min') }}</th>
                    <th class="text-right">{{ $t('Max') }}</th>
                    <th class="text-right">{{ $t('Average') }}</th>
                    <th class="text-right">{{ $t('Total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="depositsByStatus">
                    <template v-if="depositsByStatus.length">
                      <tr v-for="item in depositsByStatus" :key="item.status">
                        <td class="text-left">{{ item.status_title }}</td>
                        <td class="text-right">{{ integer(item.count) }}</td>
                        <td class="text-right">{{ decimal(item.amount_min) }}</td>
                        <td class="text-right">{{ decimal(item.amount_max) }}</td>
                        <td class="text-right">{{ decimal(item.amount_avg) }}</td>
                        <td class="text-right">{{ decimal(item.amount) }}</td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6">
                        {{ $t('No data found') }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(4).fill(0)" :key="i">
                      <td colspan="6">
                        <v-skeleton-loader type="text" />
                      </td>
                    </tr>
                  </template>
                </tbody>
              </template>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" lg="12">
        <v-card class="text-center" :loading="!withdrawalsByStatus">
          <v-toolbar>
            <v-toolbar-title class="headline font-weight-thin">
              {{ $t('Withdrawals by status') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu
              :filters="['period']"
              :disabled="!withdrawalsByStatus"
              @apply="loadData('accounting-withdrawals-by-status', $event)"
            />
          </v-toolbar>
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t('Status') }}</th>
                    <th class="text-right">{{ $t('Count') }}</th>
                    <th class="text-right">{{ $t('Min') }}</th>
                    <th class="text-right">{{ $t('Max') }}</th>
                    <th class="text-right">{{ $t('Average') }}</th>
                    <th class="text-right">{{ $t('Total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="withdrawalsByStatus">
                    <template v-if="withdrawalsByStatus.length">
                      <tr v-for="item in withdrawalsByStatus" :key="item.status">
                        <td class="text-left">{{ item.status_title }}</td>
                        <td class="text-right">{{ integer(item.count) }}</td>
                        <td class="text-right">{{ decimal(item.amount_min) }}</td>
                        <td class="text-right">{{ decimal(item.amount_max) }}</td>
                        <td class="text-right">{{ decimal(item.amount_avg) }}</td>
                        <td class="text-right">{{ decimal(item.amount) }}</td>
                      </tr>
                    </template>
                    <tr v-else>
                      <td colspan="6">
                        {{ $t('No data found') }}
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(v,i) in Array(4).fill(0)" :key="i">
                      <td colspan="6">
                        <v-skeleton-loader type="text" />
                      </td>
                    </tr>
                  </template>
                </tbody>
              </template>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { integer, decimal, percentage, short } from '~/plugins/format'
import FilterMenu from '~/components/Filters/FilterMenu'
import DashboardMixin from '~/mixins/Admin/Dashboard'

export default {
  components: { FilterMenu },

  mixins: [DashboardMixin],

  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Dashboard') + ' ' + this.$t('Accounting') }
  },

  data () {
    return {
      endpoint: '/api/admin/dashboard/payments/data',
      queries: [
        'accounting-profit',
        'accounting-balance',
        'accounting-deposits-comparison',
        'accounting-deposits-history',
        'accounting-withdrawals-history',
        'accounting-deposits-by-status',
        'accounting-withdrawals-by-status'
      ]
    }
  },

  computed: {
    profit () {
      return this.data['accounting-profit'] || null
    },
    totalProfit () {
      return this.profit ? this.profit.deposits_total - this.profit.withdrawals_total - this.profit.accounts_total : 0
    },
    balance () {
      return this.data['accounting-balance'] || null
    },
    depositsTotal () {
      return this.balance !== null ? this.balance[0] : 0
    },
    withdrawalsTotal () {
      return this.balance !== null ? this.balance[1] : 0
    },
    deposits () {
      return this.data['accounting-deposits-comparison'] || null
    },
    previousDeposits () {
      return this.deposits !== null ? this.deposits[0] : 0
    },
    currentDeposits () {
      return this.deposits !== null ? this.deposits[1] : 0
    },
    depositsByStatus () {
      return this.data['accounting-deposits-by-status'] || null
    },
    withdrawalsByStatus () {
      return this.data['accounting-withdrawals-by-status'] || null
    }
  },

  methods: {
    integer,
    decimal,
    percentage,
    short
  }
}
</script>
