<template>
  <div class="chat-list">
    <transition name="slide-right" v-on:after-leave="showChatbox">
      <div class="conversations" v-if="isConversationShow">
        <template v-for="chat in inboxChats" v-if="inboxChats.length > 0">
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
  import { mapGetters } from 'vuex'
  import { getUrlParam } from '@common/utils'

  export default {
    data() {
      return {
        isConversationShow: true,
        isChatboxShow: false,
        opponentId: null,
        opponentType: null,
        authUser: window.App.user
      }
    },
    computed: {
      ...mapGetters({
        inboxChats: 'chat/getInboxChats'
      })
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
    mounted() {
      this.fetchChats()
    },
    methods: {
      async fetchChats() {
        return this.$store.dispatch('chat/fetchInboxChats')
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
        this.isChatboxShow = false
      },
      showChatbox() {
        this.isConversationShow = false
        this.isChatboxShow = true
      },
      chatSelected(chat) {
        this.opponentId = this.getOpponentId(chat)
        this.opponentType = this.getOpponentType(chat)
        this.showChatbox()
      },
      hideChatbox() {
        this.showConversation()
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
