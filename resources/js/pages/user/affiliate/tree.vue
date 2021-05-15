<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ $t("Your Referred users") }}
          </v-card-title>
          <v-card-text>
            <v-treeview v-if="tree" :items="tree">
              <template v-slot:label="{ item }">
                {{ item.name
                }}{{
                  item.children && item.children.length
                    ? ` (${item.children.length})`
                    : ""
                }}
              </template>
            </v-treeview>
            <template v-else>
              <p>
                {{ $t("Loading...") }}
              </p>
            </template>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from "axios";
import { mapState } from "vuex";

export default {
  metaInfo() {
    return { title: this.$t("Affiliates tree") };
  },

  data() {
    return {
      tree: null,
    };
  },
  computed: {
    ...mapState("auth", ["user"]),
  },
  async created() {
    const { data } = await axios.get(
      `/api/user/affiliate/tree/${this.user.id}`
    );

    this.tree = data;
  },
};
</script>
