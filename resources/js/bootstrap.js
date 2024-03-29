import Lodash from 'lodash'
import Jquery from 'jquery'
import Axios from 'axios'
import Vue from 'vue'
import Moment from 'moment'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import SocketIOClient from 'socket.io-client'
import 'moment-timezone'
import 'moment/locale/id'
import Qs from 'qs'


window.$ = window.jQuery = Jquery
require('bootstrap')

window.Vue = Vue

window._ = Lodash
Vue.prototype._ = Lodash

window.axios = Axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.axios.interceptors.request.use(config => {
  config.paramsSerializer = params => {
    return Qs.stringify(params, {
      arrayFormat: 'indices',
      encode: false
    })
  }

  return config
})

const token = document.head.querySelector('meta[name="csrf-token"]')

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

window.moment = Moment

Vue.prototype.moment = Moment

window.io = SocketIOClient

window.Pusher = Pusher

window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: process.env.MIX_SOCKET_IO_HOST
})
