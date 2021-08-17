<template>
  <v-container fluid>
    <v-row align="center" justify="center">
      <v-col cols="12">
        <v-card>
          <v-toolbar>
          
                  <v-spacer></v-spacer>
                     <router-link :to="{ name: 'admin.rooms.create' }" class="v-btn mr-1">Create a new Room</router-link>
          </v-toolbar>
        </v-card>
      </v-col>
    </v-row>
    <data-table
      api="/api/games/casino-holdem/rooms"
      :title="'Rooms'"
      :headers="headers"
      :search="true"
      :search-placeholder="$t('Room Name')"
      apiType="GET_ROOMS"
      ref="childComponentRef"

    >
     <template v-slot:item.actions="{ item }">
            <v-btn
              type="button"
              small
              color="error"
              @click="deleteRoom(item.id)"
            >
              Delete
            </v-btn>
          </template>
    </data-table>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import axios from "axios";

export default {
  components: { DataTable},
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title:"Rooms"}
  },
  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Stakes'), value: 'stakes', sortable: false },
         {
          text: this.$t("Action"),
          value: "actions",
          sortable: false,
        },
      ]
    }
  },
  methods:{
      async deleteRoom(id) {
        const IsConfirm = window.confirm("Are you sure you want to delete this room?")
        if(IsConfirm){
      const { data } = await axios.delete(`/api/games/casino-holdem/room/delete/${id}`);
      this.$store.dispatch("message/" + (data.success ? "success" : "error"), {
        text: data.message,
      });
      this.$refs.childComponentRef.fetchData();
        }
    },
  }

}
</script>
