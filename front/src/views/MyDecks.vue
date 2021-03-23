<template>
  <div class="my-decks">
    <router-link class="button" to="/deck/create">
      <at-button type="primary" icon="icon-plus">
        Create new deck
      </at-button>
    </router-link>
    <h1>My Decks</h1>
    <section class="deck" v-for="deck in decks" :key="deck.id">
      <router-link :to="'/deck/' + deck.id">
        {{ deck.name }}
      </router-link>
    </section>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
@Component({})
export default class MyDecks extends Vue {
  mounted() {
    console.log("mounted");
    this.$store.dispatch("getMyDecks");
    this.$store.commit("resetDeck");
  }

  get decks() {
    return this.$store.state.decks;
  }
}
</script>

<style lang="scss" scoped>
h1 {
  margin-top: 10px;
  margin-bottom: 10px;
}

section {
  margin-bottom: 5px;
}

.deck {
  border-radius: 5px;
  border: 1px solid hsl(0, 0%, 80%);
  transition: 0.3s;
  a {
    display: block;
    padding: 10px;
    transition: 0.3s;
  }
  &:hover {
    border-color: $hover;
    background-color: $hover-bg;
    a {
      color: $hover;
    }
  }
}
</style>
