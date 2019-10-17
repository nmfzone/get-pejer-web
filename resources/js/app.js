import Vue from 'vue'
import store from '@store'
import './bootstrap'

import InitComponent from './components/Init.vue'

import ChatboxComponent from './components/chats/Chatbox.vue'
import ChatFormComponent from './components/chats/ChatForm.vue'
import ChatListComponent from './components/chats/ChatList.vue'

Vue.component('init', InitComponent)
Vue.component('chatbox', ChatboxComponent)
Vue.component('chat-form', ChatFormComponent)
Vue.component('chat-list', ChatListComponent)

moment.tz.setDefault('Asia/Jakarta')


new Vue({
  el: '#app',
  store
})
