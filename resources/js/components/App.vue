<template>
  <component :is="layout" v-if="layout" :class="routeClass"/>
</template>
<script>
import { config } from '~/plugins/config'
import { mapState, mapGetters } from 'vuex'

// Load layout components dynamically.
const requireContext = require.context('~/layouts', false, /.*\.vue$/)

const layouts = requireContext.keys()
  .map(file =>
    [file.replace(/(^.\/)|(\.vue$)/g, ''), requireContext(file)]
  )
  .reduce((components, [name, component]) => {
    components[name] = component.default || component
    return components
  }, {})

export default {
  data () {
    return {
      layout: null
    }
  },

  metaInfo: {
    // if no subcomponents specify a metaInfo.title, this title will be used
    title: 'Page',
    // all titles will be injected into this template
    titleTemplate: '%s | ' + config('app.name')
  },

  computed: {
    ...mapState('broadcasting', ['echo']),
    ...mapState('online', ['users']),
    ...mapGetters({ authenticated: 'auth/check' }),
    routeClass () {
      if (!this.$route.name) {
        return ''
      }

      let result = this.$route.name.replaceAll('.', '-')

      if (this.$route.name === 'game') {
        result += `-${this.$route.params.game}`
      } else if (this.$route.name === 'page') {
        result += `-${this.$route.params.id}`
      }

      return `view-${result}`
    },
    displayOnlineStatus () {
      return config('settings.interface.online.enabled')
    }
  },

  created () {
    if (this.echo) {
      if (this.displayOnlineStatus && this.authenticated) {
        this.joinOnlineChannel()
      }

      if (this.authenticated) {
        this.joinGameFeedChannel()
      }

      this.$watch('authenticated', (isAuthenticated, wasAuthenticated) => {
        console.log(isAuthenticated, wasAuthenticated)

        if (!wasAuthenticated && isAuthenticated) {
          if (this.displayOnlineStatus) {
            this.joinOnlineChannel()
          }
          this.joinGameFeedChannel()
        } else if (wasAuthenticated && !isAuthenticated) {
          if (this.displayOnlineStatus) {
            this.leaveOnlineChannel()
          }
          this.leaveGameFeedChannel()
        }
      })
    }
  },

  methods: {
    setLayout (layout) {
      this.layout = layouts[layout]
    },

    joinOnlineChannel () {
      this.echo.join('online')
        // currently joined users
        .here(users => this.$store.dispatch('online/init', { users: users.map(user => user.id) }))
        // new user joined
        .joining(user => this.$store.dispatch('online/add', { userId: user.id }))
        // user left
        .leaving(user => this.$store.dispatch('online/remove', { userId: user.id }))
        // bot joined
        .listen('UserIsOnline', user => {
          this.$store.dispatch('online/add', { userId: user.id, timeout: 300000 }) // stay online for 5 minutes
        })
    },

    leaveOnlineChannel () {
      this.echo.leave('online')
    },

    joinGameFeedChannel () {
      this.echo.private('games')
        .listen('GamePlayed', game => {
          this.$store.dispatch('game/add', game)
        })
    },

    leaveGameFeedChannel () {
      this.echo.leave('games')
    }
  }
}
</script>
