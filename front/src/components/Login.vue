<template>
  <div class="login">
    <h1>Login</h1>
    <at-alert v-if="error" id="error" type="error" :message="error" />
    <label for="user">User</label>
    <at-input id="user" v-model="user" type="text" />
    <label for="password">Password</label>
    <at-input id="password" v-model="password" type="password" />
    <at-button id="login" type="primary" @click="login">Login</at-button>
    <br />
    <br />
    <p>
      Don't have an account ?
      <br />
      <a href="/register">
        Create an account now !
      </a>
    </p>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";

@Component({ name: "Login" })
export default class Login extends Vue {
  user = "";
  password = "";

  error = "";

  async login() {
    const credentials = {
      email: this.user,
      password: this.password
    };
    const res = await this.$store.dispatch("login", credentials);
    if (res && res.error) {
      this.error = res.message;
      return;
    }
    this.$router.push("/my-decks");
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
