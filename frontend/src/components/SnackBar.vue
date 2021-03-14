<template>
  <div class="text-center ma-2">
    <v-snackbar
            absolute
            right
            :color="color"
            top
            v-model="snackbar"
    >
      {{ text }}
    </v-snackbar>
  </div>
</template>

<script>
  import EventBus from "../utils/bus";

  export default {
    name: 'SnackBar',
    data: () => ({
      snackbar: false,
      text: ``,
      color: 'warning'
    }),
    created() {
      EventBus.$on('new_notification', this.onNewNotification);
    },
    destroyed() {
      EventBus.$off('new_notification', this.onNewNotification);
    },
    methods: {
      onNewNotification(notification) {
        this.snackbar = true;
        this.text = notification.text;
        this.color = notification.color;
      },
    },
  }
</script>