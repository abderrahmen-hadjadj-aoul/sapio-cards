<template>
  <div class="login">
    <h1>Login</h1>
    <label for="user">User</label>
    <at-input id="user" type="text" v-model="user" />
    <label for="password">Password</label>
    <at-input id="password" type="password" v-model="password" />
    <at-button type="primary" @click="login">Login</at-button>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";

@Component({ name: "Login" })
export default class Login extends Vue {
  user = "";
  password = "";

  mounted() {
    console.log("login mounted");
  }

  login() {
    const credentials = {
      email: this.user,
      password: this.password
    };
    this.$store.dispatch("login", credentials);
  }
}
</script>

<style lang="scss" scoped>
.login {
  border: 2px solid hsl(0, 0%, 90%);
  border-radius: 5px;
  padding: 15px;
  & > * {
    margin-bottom: 10px;
  }
}
</style>
