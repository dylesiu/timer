<template>
  <v-dialog
          :persistent="false"
          max-width="400"
          v-model="dialog"
  >
    <v-card>
      <v-card-title class="headline">
        Nazwa zadania
      </v-card-title>

      <v-card-text>
        <v-form @submit.prevent="create">
          <v-text-field
                  autofocus
                  v-model="name"
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
                @click="create"
                color="success"
        >
          <v-icon>mdi-play</v-icon>
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'NewTaskDialog',
    data: () => ({
      dialog: false,
      name: ''
    }),
    created() {
      this.$parent.$on('open_new_task_dialog', this.open);
    },
    destroyed() {
      this.$parent.$off('open_new_task_dialog', this.open);
    },
    methods: {
      open() {
        this.name = '';
        this.dialog = true;
      },
      create() {
        this.$emit('submit', this.name);
        this.dialog = false;
      }
    },
  }
</script>
