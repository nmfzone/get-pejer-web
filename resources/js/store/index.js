import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

// Modules
import ChatModule from '@module/chats'

const state = {
  //
}

const getters = {
  //
}

const mutations = {
  SET (state, payload) {
    Vue.set(state, payload.key, payload.value)
  }
}

const actions = {
  //
}

export default new Vuex.Store({
  state,
  getters,
  mutations,
  actions,
  modules: {
    chat: ChatModule,
  }
})
