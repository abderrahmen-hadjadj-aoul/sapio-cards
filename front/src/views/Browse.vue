<template>
  <div class="browse">
    <h1>Public Decks</h1>
    <at-input v-model="search" placeholder="Search decks" prepend-button>
      <template slot="prepend">
        <i class="icon icon-search"></i>
      </template>
    </at-input>
    <p v-if="search" class="count">Found {{ decks.length }} deck(s)</p>
    <br />

    <section v-for="deck in decks" :key="deck.id" class="deck">
      <router-link :to="'/deck/' + deck.id">
        <span class="name" v-html="searched(deck.name)"></span>
        <span class="version">Version {{ deck.version }}</span>
      </router-link>
    </section>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import { Deck } from "../types";

function escapeRegExp(string: string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"); // $& means the whole matched string
}
@Component({})
export default class MyDecks extends Vue {
  search = "";

  mounted() {
    this.$store.dispatch("getPublicDecks");
    this.$store.commit("resetDeck");
  }

  searched(text: string) {
    if (!this.search) return text;
    const escaped = escapeRegExp(this.search);
    const regex = new RegExp("" + escaped + "", "i");
    return text.replace(regex, "<span>$&</span>");
  }

  get decks() {
    if (!this.search) return this.$store.state.publicDecks;
    return this.$store.state.publicDecks.filter((deck: Deck) =>
      deck.name.toLowerCase().includes(this.search.toLowerCase())
    );
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

::v-deep .name span {
  background-color: yellow;
}

.count {
  margin-top: 5px;
}

.name {
  margin-right: 20px;
}

.version {
  color: hsl(0, 0%, 40%);
}
</style>
