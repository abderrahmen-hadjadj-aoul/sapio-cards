<template>
  <div class="register">
    <h1>Create an account</h1>
    <label for="user">User</label>
    <at-input id="user" v-model="user" type="text" />
    <label for="password">Password</label>
    <at-input id="password" v-model="password" type="password" />
    <label for="password">Password verification</label>
    <at-input id="password" v-model="password2" type="password" />
    <at-button type="primary">Create account</at-button>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";

@Component({ name: "Register" })
export default class Register extends Vue {
  user = "";
  password = "";
  password2 = "";

  mounted() {
    console.log("register mounted");
  }
}
</script>

<style lang="scss" scoped>
.register {
  border: 2px solid hsl(0, 0%, 90%);
  border-radius: 5px;
  padding: 15px;
  & > * {
    margin-bottom: 10px;
  }
}
</style>
