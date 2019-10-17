import * as types from './constant'
import * as endpoints from '@constants/endpoints'

const state = {
  inboxChats: []
}

const getters = {
  getInboxChats: state => state.inboxChats
}

const mutations = {
  [types.SET_INBOX_CHATS] (state, payload) {
    state.inboxChats = payload
  }
}

const actions = {
  async fetchInboxChats ({ commit }) {
    const response = (await axios.get(endpoints.GET_INBOX_CHATS))

    const chats = _.get(response, 'data.data')

    commit(types.SET_INBOX_CHATS, chats)
  },
  addChatToInbox ({ commit, state }, chat) {
    let chats = [chat]

    _.each(state.inboxChats, (iChat) => {
      if (
        (iChat.receivable_type === 'user' &&
          (iChat.sender_id === chat.sender_id && iChat.receivable_id !== chat.receivable_id) &&
          (iChat.receivable_id === chat.receivable_id && iChat.sender_id !== chat.sender_id)) ||
        (iChat.receivable_type === 'group'
          && iChat.receivable_id !== chat.receivable_id)
      ) {
        chats.push(iChat)
      }
    })

    commit(types.SET_INBOX_CHATS, chats)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
