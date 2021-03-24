<template>
  <div id="app">
    <h1 class="brand">
      Sapio Cards
      <span>Learn efficiently with the flash card technique</span>
    </h1>

    <div id="nav">
      <router-link to="/">Home</router-link>
      <template v-if="isLogged">
        <router-link to="/browse">Browse</router-link>
        <router-link to="/my-decks">My Decks</router-link>
      </template>
      <router-link to="/about">About</router-link>
    </div>
    <router-view v-if="isLogged" />
    <div class="not-logged" v-else-if="checkedLogStatus">
      <Login />
      <Register />
    </div>
    <div v-if="!checkedLogStatus">
      Checking your account...
    </div>
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import { Component } from "vue-property-decorator";
import Login from "@/components/Login";
import Register from "./components/Register";

@Component({
  components: { Login, Register }
})
export default class Home extends Vue {
  mounted() {
    console.log("App mounted");
  }

  get isLogged() {
    return this.$store.state.isLogged;
  }

  get checkedLogStatus() {
    return this.$store.state.checkedLogStatus;
  }
}
</script>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap");

#app {
  font-family: "Roboto", sans-serif;
  width: 800px;
  margin: auto;
}

h1.brand {
  font-family: "Dancing Script", cursive;
  margin-top: 20px;
  padding: 15px;
  font-size: 25px;
  background-color: hsl(0, 0%, 95%);
  text-align: center;
  color: #1b9638;
  span {
    font-weight: normal;
    font-family: "Roboto", sans-serif;
    font-size: 17px;
    font-style: italic;
    color: hsl(0, 0%, 40%);
  }
}

#nav {
  margin-top: 10px;
  margin-bottom: 10px;
  a {
    display: inline-block;
    margin-right: 20px;
    margin-bottom: 20px;
    padding: 5px;
    padding-left: 10px;
    padding-right: 10px;
    border-bottom: 2px solid hsl(0, 0%, 80%);
    &:hover {
      background-color: $hover-bg;
      color: $hover;
    }
    &.router-link-exact-active {
      color: #389e17;
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
