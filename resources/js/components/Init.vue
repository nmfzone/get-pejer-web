<template>
  <div></div>
</template>

<script>
  export default {
    data() {
      return {
        authUser: window.App.user
      }
    },
    async mounted() {
      Echo.private(`chats.all.${this.authUser.id}`)
        .listen('Chats.NotifyNewChat', (e) => {
          this.$store.dispatch('chat/addChatToInbox', e.chat)
        })
    }
  }
</script>
