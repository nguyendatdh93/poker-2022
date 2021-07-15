import axios from 'axios'
import { route } from '~/plugins/route'

import {
  GAME_ROOM_PLAYERS, GAME_ROOM_FOLD_PLAYERS, GAME_ROOM_PLAYERS_BET, GAME_ROOM_COMMUNITY_CARD
} from '../mutation-types'

// state
export const state = {
  players: [],
  foldPlayers: [],
  playersBet: [],
  communityCard: [],
}

// mutations
export const mutations = {
  [GAME_ROOM_PLAYERS] (state, payload) {
    state.players = payload;
  },
  [GAME_ROOM_FOLD_PLAYERS] (state, payload) {
    state.foldPlayers = payload;
  },
  [GAME_ROOM_PLAYERS_BET] (state, payload) {
    state.playersBet = payload;
  },
  [GAME_ROOM_COMMUNITY_CARD] (state, payload) {
    state.communityCard = payload;
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
  },
  setPlayersBet({commit}, payload) {
    // execute the action
    commit(GAME_ROOM_PLAYERS_BET, payload);
  },
  setCommunityCard({commit}, payload) {
    // execute the action
    commit(GAME_ROOM_COMMUNITY_CARD, payload);
  }
}
