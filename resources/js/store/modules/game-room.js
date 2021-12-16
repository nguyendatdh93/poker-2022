import axios from 'axios'
import { route } from '~/plugins/route'

import {
  GAME_ROOM_PLAYERS, GAME_ROOM_FOLD_PLAYERS, GAME_ROOM_PLAYERS_BET, GAME_ROOM_COMMUNITY_CARD, GAME_ROOM_ACTION, GAME_ROOM, GAME_ROOM_CHAT, GAME_ROOM_PLAYER_CHIPS
} from '../mutation-types'

// state
export const state = {
  players: [],
  foldPlayers: [],
  playersBet: [],
  communityCard: [],
  action: [],
  gameRoom: [],
  chatDrawer: false,
  chips: [],
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
  [GAME_ROOM_ACTION] (state, payload) {
    state.action = payload;
  },
  [GAME_ROOM] (state, payload) {
    state.gameRoom = payload;
  },
  [GAME_ROOM_CHAT] (state, payload) {
    state.chatDrawer = payload;
  },
  [GAME_ROOM_PLAYER_CHIPS] (state, payload) {
    state.chips = payload;
  },
}

// actions
export const actions = {
  setGameRoom({commit}, playload) {
    commit(GAME_ROOM, playload);
  },
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
  setGameRoomChat({commit, state}, payload) {
    // execute the action
    commit(GAME_ROOM_CHAT, !state.chatDrawer);
  },
  setCommunityCard({commit}, payload) {
    // execute the action
    commit(GAME_ROOM_COMMUNITY_CARD, payload);
  },
  setChips({commit}, payload) {
    // execute the action
    commit(GAME_ROOM_PLAYER_CHIPS, payload);
  },
  async fetchCommunityCard({commit}, payload) {
    await axios.post('/api/games/casino-holdem/community-card', payload)
  },
  setAction({commit}, payload) {
    // execute the action
    commit(GAME_ROOM_ACTION, payload);
  },
  async action({commit}, payload) {
    await axios.post('/api/games/casino-holdem/action', payload)
  },
}
