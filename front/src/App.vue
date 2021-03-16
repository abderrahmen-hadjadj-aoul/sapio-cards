<template>
  <div id="app">
    <h1 class="brand">
      Sapio Cards
      <span>Learn efficiently with the flash card technique</span>
    </h1>

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
@import url("https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap");
#app {
  font-family: "Roboto", sans-serif;
  width: 800px;
  margin: auto;
}

h1.brand {
  font-family: "Dancing Script", cursive;
  margin-top: 20px;
  padding-bottom: 10px;
  font-size: 40px;
  border-bottom: 2px solid hsl(0, 0%, 90%);
  text-align: center;
  color: #1B9638;
  span {
    font-weight: normal;
    font-family: "Roboto", sans-serif;
    display: block;
    font-size: 17px;
    font-style: italic;
    color: hsl(0, 0%, 40%);
  }
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
