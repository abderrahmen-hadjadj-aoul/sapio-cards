<template>
  <div class="deck" v-if="deck">
    <h1>Deck: {{ deck.name }}</h1>
    <p>{{ deck.description }}</p>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
@Component({})
export default class Deck extends Vue {
  mounted() {
    console.log("mounted");
    this.$store.dispatch("getDeck", this.deckid);
  }

  get deckid() {
    return this.$route.params.deckid;
  }

  get deck() {
    return this.$store.state.deck;
  }
}
</script>
