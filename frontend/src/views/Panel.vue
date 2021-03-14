<template>
  <v-container class="fill-height justify-space-around align-content-center">
    <v-row class="justify-center">
      <v-col cols="12" xl="4" lg="6" md="8" sm="10">
        <v-card>
          <v-card-text>
            <Timer/>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row class="justify-center">
      <v-col cols="12">
        <ListTasks/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import Timer from "../components/timer/Timer";
  import ListTasks from "../components/timer/ListTasks";

  export default {
    name: 'Panel',
    components: {ListTasks, Timer},
    data: () => ({
      tasks: [],
      loading: false,
    }),
  }
</script>