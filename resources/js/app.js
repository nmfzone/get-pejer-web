import Vue from 'vue'
import store from '@store'
import VueMarkdown from 'vue-markdown'
import './bootstrap'
import * as utils from '@common/utils'

import InitComponent from './components/Init.vue'

import ChatboxComponent from './components/chats/Chatbox.vue'
import ChatFormComponent from './components/chats/ChatForm.vue'
import ChatListComponent from './components/chats/ChatList.vue'

Vue.component('vue-markdown', VueMarkdown)
Vue.component('init', InitComponent)
Vue.component('chatbox', ChatboxComponent)
Vue.component('chat-form', ChatFormComponent)
Vue.component('chat-list', ChatListComponent)

moment.tz.setDefault('Asia/Jakarta')

Vue.prototype.date = utils.date


new Vue({
  el: '#app',
  store
})
