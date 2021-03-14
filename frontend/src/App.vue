<template>
  <v-app :style="{'background-image': 'url(' + require('./assets/timer-bg.jpg') + ')'}">
    <SnackBar/>
    <v-main>
      <router-view/>
    </v-main>
  </v-app>
</template>

<script>
  import SnackBar from "./components/SnackBar";
  export default {
    name: 'App',
    components: {
      SnackBar
    },
    data: () => ({}),
  };
</script>