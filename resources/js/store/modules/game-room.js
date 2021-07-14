import axios from 'axios'
import { route } from '~/plugins/route'

import {
  GAME_ROOM_PLAYERS, GAME_ROOM_FOLD_PLAYERS
} from '../mutation-types'

// state
export const state = {
  players: [],
  foldPlayers: []
}

// mutations
export const mutations = {
  [GAME_ROOM_PLAYERS] (state, payload) {
    state.players = payload;
  },
  [GAME_ROOM_FOLD_PLAYERS] (state, payload) {
    state.foldPlayers = payload;
  },
}

// actions
export const actions = {
  async onPlayers ({ commit }, payload) {
    // execute the action
    const {data: players} = await axios.post('/api/games/casino-holdem/players', payload)
  },
  setPlayers({commit}, playload) {
    commit(GAME_ROOM_PLAYERS, playload);
  },
  setFoldPlayers({commit}, payload) {
    // execute the action
    commit(GAME_ROOM_FOLD_PLAYERS, payload);
  }
}
