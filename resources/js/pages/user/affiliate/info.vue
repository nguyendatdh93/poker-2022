<template>
  <v-container fluid>
    <v-row justify="center">
      <v-col cols="12" lg="6">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t("Affiliate program") }}
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-row justify="center">
              <v-col cols="12" lg="12"> </v-col>
            </v-row>
            <h2 class="title">{{ $t("About") }}</h2>

            <p>
              {{
                $t(
                  "Refer new users to our website and earn credits when they make bets or deposits."
                )
              }}
            </p>

            <p>
              {{ $t("Affiliate commission is divided into three tiers.") }}
              {{
                $t(
                  "Everyone who joins the website by the below link becomes your 1st-tier referral."
                )
              }}
              {{
                $t(
                  "2nd-tier is users who signed under the 1st-tier referrals link."
                )
              }}
              {{
                $t(
                  "3rd-tier is users who signed under the 2nd-tier referrals link."
                )
              }}
            </p>

            <p class="mb-5">
              {{
                $t("All pending commissions are reviewed by an administrator.")
              }}
              {{
                $t(
                  "Upon approval the total commissions value will be transferred to your balance."
                )
              }}
              {{
                $t(
                  "In case of abuse attempt your commissions will be rejected and your account will be suspended."
                )
              }}
              <template v-if="approvalFrequency !== 'never'">
                {{
                  $t("Commissions approval frequency: {0}.", [
                    $t(approvalFrequency),
                  ])
                }}
              </template>
            </p>

            <v-text-field
              :value="affiliateUrl"
              ref="link"
              :label="$t('Your affiliate link')"
              append-icon="mdi-content-copy"
              outlined
              readonly
              @click:append="copyToClipboard($refs.link)"
            >
              <template v-slot:append-outer>
                <v-menu offset-y>
                  <template v-slot:activator="{ on }">
                    <v-icon v-on="on" left>mdi-share-variant</v-icon>
                  </template>
                  <v-list>
                    <v-list-item
                      :href="'mailto:?body=' + affiliateUrlEncoded"
                      target="_blank"
                    >
                      <v-list-item-icon>
                        <v-icon>mdi-email</v-icon>
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>{{
                          $t("Send by email")
                        }}</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                      :href="
                        'https://web.whatsapp.com/send?text=' +
                        affiliateUrlEncoded
                      "
                      target="_blank"
                    >
                      <v-list-item-icon>
                        <v-icon>mdi-whatsapp</v-icon>
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>{{
                          $t("Send by WhatsApp")
                        }}</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                      :href="
                        'https://www.facebook.com/sharer.php?u=' +
                        affiliateUrlEncoded
                      "
                      target="_blank"
                    >
                      <v-list-item-icon>
                        <v-icon>mdi-facebook</v-icon>
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>{{
                          $t("Share on Facebook")
                        }}</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                      :href="
                        'https://twitter.com/intent/tweet?text=&url=' +
                        affiliateUrlEncoded
                      "
                      target="_blank"
                    >
                      <v-list-item-icon>
                        <v-icon>mdi-twitter</v-icon>
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>{{
                          $t("Share on Twitter")
                        }}</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </v-text-field>

            <h2 class="title mt-3">
              {{
                $t(
                  "Your upcoming dates on which 10 more users can join with your referral code"
                )
              }}
            </h2>
            <v-chip
              close-icon="mdi-delete"
              color="orange"
              link
              outlined
              pill
            >{{twoMonths}}</v-chip>
                  <v-chip
              close-icon="mdi-delete"
              color="orange"
              link
              outlined
              pill
            >{{sixMonths}}</v-chip>
            <h2 class="title mt-3">{{ $t("Paytable") }}</h2>

            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">{{ $t("Event") }}</th>
                    <th class="text-left">{{ $t("Commission type") }}</th>
                    <th class="text-right">{{ $t("1st tier") }}</th>
                    <th class="text-right">{{ $t("2nd tier") }}</th>
                    <th class="text-right">{{ $t("3rd tier") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-if="
                      commission('sign_up', 0) > 0 ||
                      commission('sign_up', 1) > 0 ||
                      commission('sign_up', 2) > 0
                    "
                  >
                    <td class="text-left">{{ $t("Referral user sign up") }}</td>
                    <td class="text-left">{{ $t("Flat") }}</td>
                    <td class="text-right">{{ commission("sign_up", 0) }}</td>
                    <td class="text-right">{{ commission("sign_up", 1) }}</td>
                    <td class="text-right">{{ commission("sign_up", 2) }}</td>
                  </tr>
                  <tr
                    v-if="
                      commission('game_loss', 0) > 0 ||
                      commission('game_loss', 1) > 0 ||
                      commission('game_loss', 2) > 0
                    "
                  >
                    <td class="text-left">
                      {{ $t("Referral user game loss") }}
                    </td>
                    <td class="text-left">{{ $t("% of bet amount") }}</td>
                    <td class="text-right">
                      {{ commission("game_loss", 0) }}%
                    </td>
                    <td class="text-right">
                      {{ commission("game_loss", 1) }}%
                    </td>
                    <td class="text-right">
                      {{ commission("game_loss", 2) }}%
                    </td>
                  </tr>
                  <tr
                    v-if="
                      commission('game_win', 0) > 0 ||
                      commission('game_win', 1) > 0 ||
                      commission('game_win', 2) > 0
                    "
                  >
                    <td class="text-left">
                      {{ $t("Referral user game win") }}
                    </td>
                    <td class="text-left">{{ $t("% of bet amount") }}</td>
                    <td class="text-right">{{ commission("game_win", 0) }}%</td>
                    <td class="text-right">{{ commission("game_win", 1) }}%</td>
                    <td class="text-right">{{ commission("game_win", 2) }}%</td>
                  </tr>
                  <tr
                    v-if="
                      commission('deposit', 0) > 0 ||
                      commission('deposit', 1) > 0 ||
                      commission('deposit', 2) > 0
                    "
                  >
                    <td class="text-left">{{ $t("Referral user deposit") }}</td>
                    <td class="text-left">{{ $t("% of deposit amount") }}</td>
                    <td class="text-right">{{ commission("deposit", 0) }}%</td>
                    <td class="text-right">{{ commission("deposit", 1) }}%</td>
                    <td class="text-right">{{ commission("deposit", 2) }}%</td>
                  </tr>
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
import { config } from "~/plugins/config";
import { mapState } from "vuex";
import { baseUrl, copyToClipboard } from "~/plugins/utils";
import axios from "axios";

export default {
  metaInfo() {
    return { title: this.$t("Affiliate program") };
  },

  data() {
    return {
      createdAt: {},
      twoMonths: "",
      sixMonths: "",
    };
  },

  computed: {
    ...mapState("auth", ["user"]),
    affiliateUrl() {
      return this.user ? baseUrl() + "/?ref=" + this.user.referral_code : "";
    },
    affiliateUrlEncoded() {
      return encodeURI(this.affiliateUrl);
    },
    approvalFrequency() {
      return config("settings.affiliate.auto_approval_frequency");
    },
  },
    watch: {
    twoMonths (value) {
      this.twoMonths=value
    },
    sixMonths (value) {
      this.sixMonths=value
    }
  },
  async created() {
    const { data } = await axios.get(
      `/api/user/available-users/${this.user.id}`
    );
    if (data.success) {
      this.twoMonths =new Date(data.twoMonth).toDateString();
      this.sixMonths = new Date(data.sixMonth).toDateString();
    }
  },
  methods: {
    commission(type, tier) {
      return config(`settings.affiliate.commissions.${type}.rates`)[tier];
    },
    copyToClipboard(ref) {
      return copyToClipboard(ref.$el.querySelector("input"));
    },
  },
};
</script>
