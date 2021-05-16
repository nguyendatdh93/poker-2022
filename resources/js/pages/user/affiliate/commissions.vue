<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" lg="8">
        <data-table
          api="/api/user/affiliate/commissions"
          :title="$t('Commissions')"
          :headers="headers"
          :filters="[
            'period',
            'affiliate-commission-type',
            'affiliate-commission-status',
          ]"
          ref="childComponentRef"
        >
          <template v-slot:item.status="{ item }">
            <span :class="getStatusClass(item)">{{ item.status_title }}</span>
          </template>
          <template v-slot:item.expires_in="{ item }">
            <span v-if="item.status == 0">{{ item.expires_in }}</span>
          </template>
          <template v-slot:item.actions="{ item }">
            <v-btn
              type="submit"
              small
              color="primary"
              @click="redeemProfit(item.id)"
              v-if="item.status == 0"
            >
              Take commission
            </v-btn>
          </template>
        </data-table>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import DataTable from "~/components/DataTable";
import axios from "axios";

export default {
  metaInfo() {
    return { title: this.$t("Affiliate program") };
  },

  components: { DataTable },

  data() {
    return {};
  },

  computed: {
    headers() {
      return [
        { text: this.$t("ID"), value: "id" },
        { text: this.$t("Tier"), value: "tier" },
        { text: this.$t("Type"), value: "title", sortable: false },
        { text: this.$t("Status"), value: "status", sortable: false },
        {
          text: this.$t("Amount"),
          value: "amount",
          align: "right",
          format: "decimal",
        },
        { text: "Expires in", value: "expires_in", sortable: false },
        {
          text: this.$t("Action"),
          value: "actions",
          sortable: false,
          align: "right",
        },
      ];
    },

  },

  methods: {
    getStatusClass(item) {
      if (item.status === 3) {
        return "green--text";
      } else if (item.status === 4) {
        return "error--text";
      }else if (item.status === 0) {
         return "warning--text";
      }
    },
    async redeemProfit(id) {
      const { data } = await axios.get(`/api/user/affiliate/${id}/redeem`);
      this.$store.dispatch("message/" + (data.success ? "success" : "error"), {
        text: data.message,
      });
      this.$refs.childComponentRef.fetchData();
    },
  },
};
</script>
