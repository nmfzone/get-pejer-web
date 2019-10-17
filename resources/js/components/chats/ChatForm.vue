<template>
  <div>
    <div class="form-group" :class="{ 'has-error': hasError }">
      <textarea
        :disabled="loading || disabled"
        class="form-control chat-form"
        v-model="content"
        rows="4"
        @keydown="sendMessage">
      </textarea>

      <span class="help-block" v-if="hasError" v-text="errorText"></span>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'

  export default {
    props: {
      receiverId: {
        type: Number,
        required: true
      },
      receiverType: {
        type: String,
        required: true
      },
      disabled: {
        type: Boolean,
        default: false
      },
    },
    data() {
      return {
        content: '',
        loading: false,
        hasError: false,
        errorText: ''
      }
    },
    mounted() {
      //
    },
    methods: {
      getReceiverType() {
        if (this.receiverType === 'groups') {
          return 'group'
        }

        return 'user'
      },
      async sendMessage(e) {
        if (e.keyCode === 13 && !e.shiftKey && this.content.trim().length > 0) {
          this.hasError = false
          this.errorText = ''
          this.loading = true

          try {
            const response = await axios.post('/api/chats', {
              message: this.content,
              receiver_id: this.receiverId,
              receiver_type: this.getReceiverType()
            }, {
              params: {
                includes: ['sender', 'receivable'],
              }
            })
            this.content = ''
            this.$emit('chat-created', _.get(response, 'data.data'))
          } catch (e) {
            this.hasError = true
            this.errorText = 'Gagal mengirim pesan! Terdapat kesalahan.'
          }

          this.loading = false
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .loading-indicator {
    padding-bottom: 10px
  }
  .chat-form {
    margin-top: 20px;
    &:disabled {
        background-color: #ffffff;
    }
  }
</style>
