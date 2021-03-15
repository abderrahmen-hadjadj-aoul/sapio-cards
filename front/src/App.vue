<template>
  <div id="app">
    <div id="nav">
      <router-link to="/">Home</router-link>
      <router-link to="/my-decks">My Decks</router-link>
      <router-link to="/about">About</router-link>
    </div>
    <router-view />
  </div>
</template>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");
#app {
  font-family: "Roboto", sans-serif;
  width: 800px;
  margin: auto;
}

#nav {
  margin-top: 20px;
  margin-bottom: 20px;
  a {
    display: inline-block;
    margin-right: 10px;
    padding: 5px;
    padding-left: 10px;
    padding-right: 10px;
    border-radius: 5px;
    background-color: hsl(0, 0%, 97%);
    &:hover {
      background-color: #389E17;
      color: white;
    }
    &.router-link-active {
      color: #389E17;
      &:hover {
        color: white;
      }
    }
  }
}
</style>
