<template>
  <div id="app">

    <div id="top-message" v-if="!online">
      You are offline
    </div>

    <h1 class="brand">
      SAPIO CARDS
      <br />
      <span>Learn efficiently with the flash card technique</span>
    </h1>

    <div id="nav">
      <router-link to="/">Home</router-link>
      <template v-if="isLogged">
        <router-link to="/browse">Browse</router-link>
        <router-link to="/my-decks">My Decks</router-link>
      </template>
      <router-link to="/about">About</router-link>
      <template v-if="isLogged">
        <router-link to="/logout" @click.native="logout">
          Logout ({{ user.email }})
        </router-link>
      </template>
    </div>
    <router-view v-if="isLogged" />
    <div class="not-logged" v-else-if="checkedLogStatus">
      <Login />
    </div>
    <div v-if="!checkedLogStatus">
      Checking your account...
    </div>
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import { Component } from "vue-property-decorator";
import Login from "@/components/Login.vue";
import Register from "@/components/Register.vue";

@Component({
  components: { Login, Register }
})
export default class Home extends Vue {
  mounted() {
    console.log("App mounted");
  }

  get online() {
    return this.$store.state.online;
  }

  get isLogged() {
    return this.$store.state.isLogged;
  }

  get checkedLogStatus() {
    return this.$store.state.checkedLogStatus;
  }

  get user() {
    return this.$store.state.user;
  }

  async logout() {
    this.$store.dispatch("logout");
  }
}
</script>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap");

#top-message {
  padding: 5px;
  text-align: center;
  color: #C82A1D;
  background-color: #FFE3E0;
  font-weight: bold;
}

#app {
  font-family: "Roboto", sans-serif;
  width: 800px;
  margin: auto;
}

h1.brand {
  padding: 10px;
  font-size: 18px;
  text-align: center;
  color: #1b9638;
  font-weight: normal;
  border-bottom: 2px solid hsl(0, 0%, 90%);
  margin-bottom: 25px;
  span {
    font-size: 14px;
    color: hsl(0, 0%, 40%);
  }
}

#nav {
  margin-top: 10px;
  margin-bottom: 10px;
  a {
    display: inline-block;
    margin-right: 20px;
    margin-bottom: 10px;
    padding: 5px;
    padding-left: 10px;
    padding-right: 10px;
    background-color: hsl(0, 0%, 96%);
    border-bottom: 2px solid transparent;
    &:hover {
      background-color: $hover-bg;
      color: $hover;
    }
    &.router-link-exact-active {
      color: #389e17;
      border-color: #389e17;
    }
  }
}

.not-logged {
  display: flex;
  & > * {
    &::first {
      margin-right: 5px;
    }
    flex: 1;
  }
}
</style>
