import Vue from 'vue'
import './bootstrap'

import ChatboxComponent from './components/chats/Chatbox.vue'
import ChatFormComponent from './components/chats/ChatForm.vue'
import ChatListComponent from './components/chats/ChatList.vue'


moment.tz.setDefault('Asia/Jakarta')

Vue.component('chatbox', ChatboxComponent)
Vue.component('chat-form', ChatFormComponent)
Vue.component('chat-list', ChatListComponent)

new Vue({
  el: '#app',
})
