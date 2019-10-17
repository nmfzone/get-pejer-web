<template>
  <div>
    <div class="back-btn btn btn-primary" @click="back">
      <i class="fa fa-arrow-left"></i> Kembali
    </div>
    <div class="chat-list" ref="chatList">
      <template v-if="error">
        <div class="notice error-chat">
          Terdapat kesalahan! Tidak bisa menampilkan chat.
        </div>
      </template>
      <template v-if="chats.length === 0 && !loading && !error">
        <div class="notice empty-chat">
          Mulai sebuah percakapan
        </div>
      </template>
      <template v-for="chat in chats">
        <div class="date-separator" v-if="showDateSeparator(chat)">
          <div class="content">
            {{ formatDateSeparator(chat) }}
          </div>
        </div>
        <div :key="chat.id" :class="[`chat-${chat.id}`, 'chat']">
          <div :class="['chat-inner', chat.sender_id === authUser.id ? 'current-user' : '']">
            <div class="chat-detail">
              <b class="chat-name">{{ formatChatName(chat) }}</b>

              <div class="chat-date pull-right">
                {{ moment(chat.created_at).format('HH:mm') }}
              </div>
            </div>
            <div class="chat-content" v-html="formatChatContent(chat.message)"></div>
          </div>
        </div>
      </template>
    </div>
    <chat-form
      @chat-created="pushChat"
      :receiver-id="opponentId"
      :receiver-type="opponentType"
      :disabled="formDisabled" />
  </div>
</template>

<script>
  import moment from 'moment'

  export default {
    props: {
      opponentId: {
        type: Number,
        required: true
      },
      opponentType: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        chats: [],
        loading: true,
        error: false,
        formDisabled: false,
        authUser: window.App.user
      }
    },
    async mounted() {
      try {
        const response = (await axios.get(`/api/chats/${this.opponentType}/${this.opponentId}`))

        const chats = _.get(response, 'data.data')

        _.each(chats, (chat) => {
          this.chats.unshift(chat)
        })

        let echo = null

        if (this.opponentType === 'users') {
          echo = Echo.private(`chats.users.${this.authUser.id}.to.${this.opponentId}`)
        } else if (this.opponentType === 'groups') {
          echo = Echo.private(`chats.groups.${this.opponentId}`)
        } else {
          throw new Error('Type isn\'t supported.');
        }

        echo.listen('Chats.ChatCreated', (e) => {
          this.pushChat(e.chat)
        })

        this.scrollToBottom()
        window.addEventListener('keydown', this.onKeyDown)
        this.loading = false
      } catch (e) {
        this.loading = false
        this.error = true
        this.formDisabled = true

        throw e
      }
    },
    methods: {
      pushChat(data) {
        this.chats.push(data)
        this.scrollToBottom()
      },
      scrollToBottom() {
        this.$nextTick(() => {
          this.$refs.chatList.scrollTop = this.$refs.chatList.scrollHeight
        })
      },
      showDateSeparator(chat) {
        const index = _.findIndex(this.chats, o => o.id === chat.id)

        if (index > 0) {
          const dateBefore = moment(this.chats[index-1].created_at)
          const date = moment(chat.created_at)

          return dateBefore.format('d/M/Y') !== date.format('d/M/Y')
        }

        return true
      },
      formatDateSeparator(chat) {
        const date = moment(chat.created_at)

        if (date.format('Y') !== moment().format('Y')) {
          return date.format('MMM Y')
        } else if (date.format('M') !== moment().format('M')) {
          return date.format('MMM')
        } else if (date.format('d') === moment().format('d')) {
          return 'Hari Ini'
        }

        return date.format('dddd')
      },
      formatChatName(chat) {
        return chat.sender_id === this.authUser.id ? 'Anda' : chat.sender.name
      },
      formatChatContent(content) {
        return content.replace(/(?:\r\n|\r|\n)/g, '<br>')
      },
      back() {
        window.removeEventListener('keydown', this.onKeyDown)
        this.$emit('back')
      },
      onKeyDown(e) {
        if (e.keyCode === 37) {
          this.back()
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .back-btn {
    cursor: pointer;
    margin-bottom: 20px;
  }
  .chat-list {
    height: 500px;
    overflow: scroll;
    margin-top: -10px;

    .notice {
      margin-top: 20px;
      border-radius: 10px;
      color: #ffffff;
      padding: 10px;
      background: #444444;

      &.error-chat {
        background: #ff0000;
      }
    }
  }
  .chat-detail {
    .chat-date {
      font-size: 12px;
    }
  }
  .date-separator {
    margin: 10px 0 10px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;

    .content {
      font-size: 12px;
      text-align: center;
      padding: 5px 10px 5px 10px;
      background: #e0e0e0;
      border-radius: 10px;
    }
  }
  .chat {
    display: flex;
    width: 100%;

    .chat-inner {
      padding: 10px;
      width: 50%;

      .chat-detail {

      }
      .chat-content {
        padding: 10px;
        background: #cccccc;
        border-radius: 10px;
        overflow-wrap: break-word;
        word-wrap: break-word;
        hyphens: auto;
      }

      &.current-user {
        margin-left: 50%;

        .chat-content {
          background: #adff2f;
        }
      }
    }
  }
</style>
