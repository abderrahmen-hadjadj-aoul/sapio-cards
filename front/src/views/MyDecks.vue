<template>
  <div class="my-decks">
    <h1>My Decks</h1>
    <router-link to="/deck/create">
      Create new deck
    </router-link>
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
