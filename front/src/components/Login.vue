<template>
  <div class="login">
    <h1>Login</h1>
    <at-alert type="error" v-if="error" :message="error" />
    <label for="user">User</label>
    <at-input id="user" type="text" v-model="user" />
    <label for="password">Password</label>
    <at-input id="password" type="password" v-model="password" />
    <at-button type="primary" @click="login">Login</at-button>
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

  mounted() {
    console.log("login mounted");
  }

  async login() {
    const credentials = {
      email: this.user,
      password: this.password
    };
    const res = await this.$store.dispatch("login", credentials);
    console.log("res", res);
    if (res.error) {
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
