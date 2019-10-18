<template>
  <div class="chat-list">
    <transition name="slide-right" v-on:after-leave="showChatbox">
      <div class="conversations" v-if="isConversationShow">
        <template v-for="chat in inboxChats" v-if="inboxChats.length > 0">
          <a :href="`?with=${getOpponentId(chat)}&type=${getOpponentType(chat)}`" class="user-box">
            <div class="conversation-box" :key="chat.id" @click.prevent="chatSelected(chat)">
              <div class="d-flex">
                <div class="user-photo" :style="photoBoxStyle">
                  <div class="photo-img rounded-circle" :style="photoStyle"></div>
                </div>

                <div class="chat-detail" :style="chatDetailBoxStyle">
                  <div class="opponent-name">
                    {{ getOpponentName(chat) }}
                    ({{ chat.receivable_type === 'group' ? 'G' : 'U' }})
                  </div>

                  <div class="last-message" v-html="formatLastMessage(chat)"></div>
                </div>
              </div>
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
        authUser: window.App.user,
        photoBoxStyle: {
          width: '20%',
          paddingRight: '4%'
        },
        photoStyle: {
          width: '40px',
          height: '40px'
        },
        chatDetailBoxStyle: {
          width: '75%'
        }
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
    watch: {
      inboxChats(v) {
        this.$nextTick(() => {
          this.syncGridStyle()
        })
      }
    },
    async mounted() {
      await this.fetchChats()

      this.$nextTick(() => {
        this.syncGridStyle()
        window.addEventListener('resize', this.syncGridStyle)
      })
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
      formatLastMessage(chat) {
        const senderName = chat.receivable_type === 'group'
          ? (chat.sender_id === this.authUser.id ? 'Anda' : chat.sender.name) + ':'
          : ''

        const message = _.truncate(chat.message.replace(/(?:\r\n|\r|\n)/g, ' '), {
          length: 60
        })

        return senderName + ' ' + message
      },
      syncGridStyle () {
        const windowSize = window.innerWidth

        if (windowSize >= 992) {
          this.photoBoxStyle.width = '18%'
          this.photoBoxStyle.paddingRight = '0'
          this.photoStyle.width = '70px'
        }

        if (windowSize < 992) {
          this.photoBoxStyle.width = '20%'
          this.photoBoxStyle.paddingRight = '4%'
          this.photoStyle.width = '60px'
        }

        if (windowSize < 768) {
          this.photoBoxStyle.width = '20%'
          this.photoBoxStyle.paddingRight = '4%'
          this.photoStyle.width = '40px'
        }

        this.photoStyle.height = this.photoStyle.width
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

    .conversation-box {
      width: 100%;
      border-radius: 5px;
      padding: 20px 15px 20px 15px;
      background: #444444;
      color: #ffffff;
      cursor: pointer;
      margin-bottom: 5px;

      .user-photo {
        float: left;
        display: flex;
        align-items: center;
        justify-content: center;

        .photo-img {
          background: #ccc;
          width: 40px;
          height: 40px;
        }
      }

      .chat-detail {
        float: left;

        .opponent-name {
          font-size: 16px;
          color: #14d850;
        }

        .last-message {
          overflow-wrap: break-word;
          word-wrap: break-word;
          word-break: break-all;
          hyphens: auto;
        }
      }

      &:hover {
        background: #692f2f;
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
