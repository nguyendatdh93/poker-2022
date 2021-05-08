import {
  ONLINE_INIT,
  ONLINE_ADD,
  ONLINE_REMOVE
} from '../mutation-types'

// state
export const state = {
  users: []
}

// mutations
export const mutations = {
  [ONLINE_INIT] (state, { users }) {
    state.users = [...users]
  },

  [ONLINE_ADD] (state, { userId, timeout = 0 }) {
    if (state.users.indexOf(userId) === -1) {
      state.users.push(userId)

      if (timeout > 0) {
        setTimeout(() => state.users.splice(state.users.indexOf(userId), 1), timeout)
      }
    }
  },

  [ONLINE_REMOVE] (state, { userId }) {
    const id = state.users.indexOf(userId)

    if (id > -1) {
      state.users.splice(id, 1)
    }
  }
}

// actions
export const actions = {
  init ({ commit }, payload) {
    commit(ONLINE_INIT, payload)
  },

  add ({ commit }, payload) {
    commit(ONLINE_ADD, payload)
  },

  remove ({ commit }, payload) {
    commit(ONLINE_REMOVE, payload)
  }
}
