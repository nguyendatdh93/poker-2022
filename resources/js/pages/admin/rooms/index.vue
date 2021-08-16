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
    >
      <!-- <template v-slot:item.name="{ item: { account: { user } } }">
        <user-link :user="user" />
      </template>
      <template v-slot:item.status="{ item }">
        <span :class="getStatusClass(item)">{{ item.status_title }}</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <affiliate-commission-menu :id="item.id" small />
      </template> -->
    </data-table>
  </v-container>
</template>

<script>
import Form from 'vform'
import DataTable from '~/components/DataTable'

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
      ]
    }
  },

}
</script>
