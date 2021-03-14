<template>
  <v-container class="fill-height">
    <v-row class="text-center justify-center align-content-center">
      <v-col cols="12" lg="4" sm="6">
        <v-card>
          <v-card-text>
            <LoginForm/>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import LoginForm from "../components/LoginForm";

  export default {
    name: 'Login',
    components: {LoginForm},
  }
</script>