import store from '~/store'

export default async (to, from, next) => {
  if (!store.getters['auth/check']) {
    next({ path: '/login', query: {play: to.query.play} })
  } else {
    next()
  }
}
