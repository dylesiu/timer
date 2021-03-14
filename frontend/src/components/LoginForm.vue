<template>
  <v-container>
    <v-row class="text-center">
      <v-col cols="12">
        <v-form
                @submit.prevent="login"
                lazy-validation
                ref="form"
                v-model="valid"
        >
          <v-text-field
                  label="Login"
                  prepend-icon="mdi-account"
                  required
                  v-model="username"
          />

          <v-text-field
                  label="Hasło"
                  prepend-icon="mdi-key"
                  required
                  type="password"
                  v-model="password"
          />

          <v-alert
                  class="text-left"
                  dense
                  outlined
                  type="error"
                  v-if="!valid"
          >
            {{ errorText }}
          </v-alert>

          <v-btn
                  :disabled="loading"
                  :loading="loading"
                  @click.prevent="login"
                  color="primary"
                  type="submit"
          >
            Zaloguj się
          </v-btn>
          <v-spacer/>
          <v-btn
                  :disabled="registering"
                  :loading="registering"
                  @click.prevent="register"
                  class="mt-2"
                  color="grey"
                  small
                  text
                  type="submit"
                  v-if="!newUser"
          >
            Wygeneruj nowego użytkownika
          </v-btn>
        </v-form>
      </v-col>
    </v-row>
    <v-row class="text-center align-center" v-if="!!newUser">
      <v-col cols="12">
        <span class="text-h6">Nowy użytkownik:</span>
      </v-col>
      <v-col cols="12">
        Login:
        <v-chip color="teal lighten-4">{{ newUser.username }}</v-chip>
        Hasło:
        <v-chip color="blue lighten-4">{{ newUser.password }}</v-chip>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import api from "../utils/api";

  export default {
    name: 'LoginForm',
    data: () => ({
      valid: true,
      errorText: '',
      loading: false,
      username: '',
      password: '',
      registering: false,
      newUser: null
    }),
    methods: {
      login() {
        this.loading = true;

        api.post("/login", {
          username: this.username,
          password: this.password
        })
          .then(response => {
            localStorage.setItem('app_user', JSON.stringify(response.data));
            this.$router.replace('/');
          })
          .catch((error) => {
            this.valid = false;
            this.errorText = error.response.data.error;
          })
          .finally(() => {
            this.loading = false
          });
      },
      register() {
        this.registering = true;

        api.post("/register")
          .then(response => {
            this.newUser = response.data;
          })
          .finally(() => {
            this.registering = false
          });
      }
    },
  }
</script>