<template>
  <div class="text-center" v-if="loading">
    <v-progress-circular color="accent" indeterminate size="50"/>
  </div>
  <div v-else>
    <v-row class="d-flex align-center">
      <v-col cols="12" sm="8">
        <v-btn
                :disabled="creating || !!task"
                :loading="creating"
                @click="openNewTaskDialog"
                block
                color="primary"
                large
        >
          Zarejestruj czas
        </v-btn>
      </v-col>
      <v-col class="align-center text-center" cols="6" sm="2">
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn :color="activeList ? 'info' : 'grey'" @click="toggleList" block dark v-bind="attrs" v-on="on">
              <v-icon>mdi-view-list</v-icon>
            </v-btn>
          </template>
          <span>Pokaż / ukryj listę</span>
        </v-tooltip>
      </v-col>
      <v-col cols="6" sm="2">
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                    color="orange"
                    dark
                    @click="logout"
                    v-bind="attrs"
                    v-on="on"
                    block
            >
              <v-icon>mdi-logout</v-icon>
            </v-btn>
          </template>
          <span>Wyloguj się</span>
        </v-tooltip>
      </v-col>
    </v-row>
    <NewTaskDialog
            @submit="createTimer"
    />
    <StopTaskDialog
            @submit="stopTimer"
    />
    <v-row class="mt-2 d-flex align-center" v-if="!!task">
      <v-divider class="mx-3"/>
      <v-col class="text-center" cols="12">
        <span class="subtitle-1">Trwające zadanie:</span> <i class="body-2">"{{ task.name }}"</i>
      </v-col>
      <v-col class="d-flex justify-space-between" cols="12" sm="6">
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn :disabled="running || processing" :loading="resuming" @click="resumeTimer" color="success"
                   v-bind="attrs"
                   v-on="on">
              <v-icon>mdi-play</v-icon>
            </v-btn>
          </template>
          <span>Wznów</span>
        </v-tooltip>
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn :disabled="!running || processing" :loading="pausing" @click="pauseTimer" color="warning"
                   v-bind="attrs"
                   v-on="on">
              <v-icon>mdi-pause</v-icon>
            </v-btn>
          </template>
          <span>Wstrzymaj zadanie</span>
        </v-tooltip>
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn :disabled="processing || stopping" @click="openStopTaskDialog" color="error" v-bind="attrs"
                   v-on="on">
              <v-icon>mdi-stop</v-icon>
            </v-btn>
          </template>
          <span>Zakończ zadanie</span>
        </v-tooltip>
      </v-col>
      <v-col class="align-center text-center" cols="12" sm="6">
        <p class="ma-0 clock">{{ formattedTime }}</p>
      </v-col>
    </v-row>
  </div>
</template>

<script>
  import Clock from "../../utils/Clock";
  import TaskProvider from "../../providers/task/index";
  import PauseProvider from "../../providers/pause/index";
  import NewTaskDialog from "./NewTaskDialog";
  import StopTaskDialog from "./StopTaskDialog";
  import EventBus from "../../utils/bus";

  export default {
    name: 'Timer',
    components: {StopTaskDialog, NewTaskDialog},
    data: () => ({
      newTaskDialog: false,
      loading: true,
      creating: false,
      stopping: false,
      processing: false,
      resuming: false,
      pausing: false,
      clock: null,
      seconds: 0,
      breakTime: 0,
      startedDateTime: null,
      task: null,
      pause: null,
      activeList: false
    }),
    mounted() {
      TaskProvider.current().then((response) => {
        const {task, pause, breakTime} = response.data;

        if (task) {
          this.task = task;
        }

        if (pause) {
          this.pause = pause
        }

        if (breakTime) {
          this.breakTime = breakTime;
        }

        this.startTimer();

        this.loading = false;
      })
    },
    destroyed() {
      if (this.clock) {
        this.clock.clear();
      }
    },
    computed: {
      running() {
        if (!this.task) {
          return false;
        }

        return this.task.state === 'running';
      },
      formattedTime() {
        if (!this.startedDateTime) {
          return '00:00:00';
        }

        const seconds = this.seconds - this.breakTime;
        return new Date(seconds * 1000).toISOString().substr(11, 8);
      },
    },
    methods: {
      onClockTick(seconds) {
        this.seconds = seconds;
      },
      logout() {
        localStorage.removeItem('app_user');
        this.$router.replace('/login');
      },
      toggleList() {
        this.activeList = !this.activeList;
        EventBus.$emit('list_toggle');
      },
      openNewTaskDialog() {
        this.$emit('open_new_task_dialog');
      },
      openStopTaskDialog() {
        this.$emit('open_stop_task_dialog');
      },
      createTimer(name) {

        if (!name) {
          return;
        }

        this.creating = true;

        TaskProvider.create(name).then((response) => {
          this.task = response.data;
          this.startTimer();
          this.creating = false;
        }).catch();
      },
      startTimer() {
        if (!this.task) {
          return;
        }

        this.startedDateTime = new Date(this.task.start * 1000);

        if (this.running) {
          this.clock = new Clock(this.startedDateTime, this.onClockTick);
          this.clock.start();
        } else {
          this.seconds = Math.abs(((new Date()).getTime() - this.startedDateTime.getTime()) / 1000);
        }
      },
      pauseTimer() {
        this.processing = true;
        this.pausing = true;

        PauseProvider.create(this.task.id).then((response) => {
          this.pause = response.data;
          this.task.state = 'pause';
          this.clock.pause();
        }).finally(() => {
          this.processing = false;
          this.pausing = false;
        });

      },
      resumeTimer() {
        this.processing = true;
        this.resuming = true;

        PauseProvider.end(this.pause.id).then((response) => {
          this.breakTime = response.data.breakTime;

          if (!this.clock) {
            this.clock = new Clock(this.startedDateTime, this.onClockTick);
          }

          this.task.state = 'running';
          this.clock.start();
        }).finally(() => {
          this.processing = false;
          this.resuming = false;
        });
      },
      stopTimer(description) {
        this.processing = true;
        this.stopping = true;

        TaskProvider.stop(this.task.id, description).then((task) => {
          if (this.clock) {
            this.clock.clear();
          }

          EventBus.$emit('end_task', task);

          this.task = null;
          this.seconds = 0;
          this.breakTime = 0;
        }).finally(() => {
          this.processing = false;
          this.stopping = false;
        })
      }
    },
  }
</script>
<style>
  .clock {
    font-size: 30px;
  }
</style>
