<template>
  <v-badge overlap dot :value="displayOnlineStatus" :color="users.indexOf(user.id) > -1 ? 'green' : 'red'">
    <v-avatar :size="size">
      <v-img :src="user.avatar_url" />
    </v-avatar>
  </v-badge>
</template>

<script>
import { config } from '~/plugins/config'
import { mapState } from 'vuex'

export default {
  props: {
    user: {
      type: Object,
      required: true
    },
    size: {
      type: Number,
      required: false,
      default: 35
    }
  },

  computed: {
    ...mapState('online', ['users']),

    displayOnlineStatus () {
      return config('settings.interface.online.enabled')
    }
  }
}
</script>
