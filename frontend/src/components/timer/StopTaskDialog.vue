<template>
  <v-dialog
          :persistent="false"
          max-width="400"
          v-model="dialog"
  >
    <v-card>
      <v-card-title class="headline">
        Opis wykonanego zadania
      </v-card-title>

      <v-card-text>
        <v-form @submit.prevent="submit">
          <v-textarea
                  autofocus
                  v-model="description"
          />
        </v-form>
      </v-card-text>

      <v-card-actions>
        <v-spacer/>
        <v-btn
                @click="dialog = false"
                color="grey"
                text
        >
          Zamknij
        </v-btn>

        <v-btn
                @click="submit"
                color="error"
        >
          <v-icon>mdi-stop</v-icon>
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'StopTaskDialog',
    data: () => ({
      dialog: false,
      description: ''
    }),
    created() {
      this.$parent.$on('open_stop_task_dialog', this.open);
    },
    destroyed() {
      this.$parent.$off('open_stop_task_dialog', this.open);
    },
    methods: {
      open() {
        this.description = '';
        this.dialog = true;
      },
      submit() {
        this.$emit('submit', this.description);
        this.dialog = false;
      }
    },
  }
</script>
