<template>
  <div class="chat-list">
    <transition name="slide-right" v-on:after-leave="showChatbox">
      <div class="conversations" v-if="isConversationShow">
        <template v-for="chat in chats" v-if="chats.length > 0">
          <a :href="`?with=${getOpponentId(chat)}&type=${getOpponentType(chat)}`" class="user-box">
            <div class="user" :key="chat.id" @click.prevent="chatSelected(chat)">
                {{ getOpponentName(chat) }} <span class="btn btn-success">{{ getOpponentType(chat) }}</span>
            </div>
          </a>
        </template>
      </div>
    </transition>
    <transition name="slide-left" @after-leave="showConversation">
      <chatbox
        v-if="isChatboxShow"
        @back="hideChatbox"
        :opponent-id="opponentId"
        :opponent-type="opponentType" />
    </transition>
  </div>
</template>

<script>
  import { getUrlParam } from '@common/utils'

  export default {
    data() {
      return {
        chats: [],
        isConversationShow: true,
        isChatboxShow: false,
        opponentId: null,
        opponentType: null,
        authUser: window.App.user
      }
    },
    beforeMount() {
      const opponentId = getUrlParam('with')
      const opponentType = getUrlParam('type')

      if (opponentId && opponentType) {
        this.showChatbox()
        this.isConversationShow = false
        this.opponentId = parseInt(opponentId)
        this.opponentType = opponentType
      }
    },
    async mounted() {
      this.fetchChats()

      Echo.private(`chats.all.${this.authUser.id}`)
        .listen('ChatCreated', (e) => {
          this.fetchChats()
        })
    },
    methods: {
      async fetchChats() {
        const response = (await axios.get('/api/chats/conversations'))

        this.chats = _.get(response, 'data.data')
      },
      getOpponentId(chat) {
        if (chat.sender_id === this.authUser.id || chat.receivable_type === 'group') {
          return chat.receivable_id
        }

        return chat.sender_id
      },
      getOpponentType(chat) {
        if (chat.receivable_type === 'group') {
          return 'groups'
        } else {
          return 'users'
        }
      },
      getOpponentName(chat) {
        if (chat.sender_id === this.authUser.id || chat.receivable_type === 'group') {
          return chat.receivable.name
        }

        return chat.sender.name
      },
      showConversation() {
        this.isConversationShow = true
      },
      showChatbox() {
        this.isChatboxShow = true
      },
      chatSelected(chat) {
        this.isConversationShow = false
        this.opponentId = this.getOpponentId(chat)
        this.opponentType = this.getOpponentType(chat)
      },
      hideChatbox() {
        this.isChatboxShow = false
        this.opponentType = null
        this.opponentId = null
      }
    }
  }
</script>

<style lang="scss" scoped>
  .chat-list {
    width: 100%;
    height: 100%;
    overflow: hidden;
  }
  .conversations {
    height: 500px;
    overflow: scroll;

    .user-box {
      color: #ffffff;

      &:link, &:visited, &:hover {
        text-decoration: none;
        color: #444444;
      }
    }

    .user {
      width: 100%;
      border-radius: 5px;
      padding: 20px 15px 20px 15px;
      background: #444444;
      color: #ffffff;
      cursor: pointer;
      margin-bottom: 5px;

      &:hover {
        background: #7cfc00;
        color: #444444;
      }
    }
  }

  .slide-right-enter-active {
    transition: all .2s ease;
  }
  .slide-right-leave-active {
    transition: all .3s ease;
  }
  .slide-right-enter, .slide-right-leave-to {
    transform: translateX(100%);
    opacity: 0;
  }

  .slide-left-enter-active {
    transition: all .2s ease;
  }
  .slide-left-leave-active {
    transition: all .3s ease;
  }
  .slide-left-enter, .slide-left-leave-to {
    transform: translateX(-100%);
    opacity: 0;
  }
</style>
