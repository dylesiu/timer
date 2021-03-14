<template>
  <v-card v-if="show">
    <v-card-subtitle>
      Zarejestrowane zadania
    </v-card-subtitle>
    <v-card-text>
      <v-row class="d-flex align-center">
        <v-col cols="8" md="4" sm="6">
          <v-menu
                  :close-on-content-click="false"
                  min-width="auto"
                  offset-y
                  v-model="menu"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                      hide-details
                      label="Zakres dat"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-model="dates"
                      v-on="on"
              />
            </template>
            <v-date-picker
                    @change="refreshList"
                    range
                    v-model="dates"
            />
          </v-menu>
        </v-col>
        <v-col cols="4" md="4" sm="6">
          <v-spacer/>
          <v-btn
                  color="grey"
                  outlined
                  text
                  target="_blank"
                  :href="getDownloadUrl()"
                  link
          >
            <v-icon>mdi-download</v-icon>
          </v-btn>
        </v-col>
      </v-row>
      <v-row>

      </v-row>
      <v-card-text class="text-center" v-if="loading">
        <v-progress-circular color="accent" indeterminate size="50"/>
      </v-card-text>
      <v-simple-table class="list-task-table" v-else>
        <template v-slot:default>
          <thead>
          <tr>
            <th class="text-left">
              Nazwa
            </th>
            <th class="text-left">
              Opis
            </th>
            <th class="text-left">
              Start
            </th>
            <th class="text-left">
              Koniec
            </th>
            <th class="text-left">
              Czas faktyczny
            </th>
            <th class="text-left">
              Czas rzeczywisty
            </th>
            <th class="text-left">
              Liczba przerw
            </th>
          </tr>
          </thead>
          <tbody>
          <tr
                  :key="item.id"
                  v-for="item in tasks"
          >
            <td>{{ item.name }}</td>
            <td>{{ item.description }}</td>
            <td>{{ (new Date(item.start * 1000)).toLocaleString() }}</td>
            <td>{{ (new Date(item.end * 1000)).toLocaleString() }}</td>
            <td>{{ formattedTime(item.time) }}</td>
            <td>{{ formattedTime(item.end - item.start) }}</td>
            <td>{{ item.amountBreaks }}</td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>
      <v-alert class="mt-2" color="info" outlined v-if="!tasks.length && !loading">
        Brak zarejestrowanych zadań
      </v-alert>
    </v-card-text>
  </v-card>
</template>

<script>
  import TaskProvider from "../../providers/task/index";
  import EventBus from "../../utils/bus";

  export default {
    name: 'ListTasks',
    data: () => ({
      show: false,
      tasks: [],
      loading: false,
      dates: null,
      menu: null
    }),
    created() {
      EventBus.$on('list_toggle', this.toggleDisplay);
      EventBus.$on('end_task', this.onEndTask);
    },
    destroyed() {
      EventBus.$off('list_toggle', this.toggleDisplay);
      EventBus.$off('end_task', this.onEndTask);
    },
    methods: {
      toggleDisplay() {
        this.show = !this.show;

        if (this.show) {
          this.refreshList();
        } else {
          this.menu = false;
        }
      },
      refreshList() {
        this.loading = true;
        TaskProvider.getAll(this.dates).then((tasks) => {
          this.tasks = tasks;
        }).finally(() => this.loading = false);
      },
      formattedTime(seconds) {
        return new Date(seconds * 1000).toISOString().substr(11, 8);
      },
      onEndTask(task) {
        if (this.show) {
          this.tasks.unshift(task);
        }
      },
      getDownloadUrl() {
        return TaskProvider.download(this.dates);
      }
    },
  }
</script>
<style>
  .list-task-table {
    max-height: 400px;
    overflow: auto;
  }
</style>
