<template>
  <div class="my-decks">
    <router-link class="button" to="/deck/create">
      <at-button type="primary" icon="icon-plus">
        Create new deck
      </at-button>
    </router-link>

    <h1>My Decks</h1>

    <at-radio-group v-model="type">
      <at-radio-button label="mine">Mine</at-radio-button>
      <at-radio-button label="favorites">Favorites</at-radio-button>
    </at-radio-group>

    <at-input v-model="search" placeholder="Search decks" prepend-button>
      <template slot="prepend">
        <i class="icon icon-search"></i>
      </template>
    </at-input>
    <p v-if="search" class="count">Found {{ decks.length }} deck(s)</p>
    <br />

    <section class="deck" v-for="deck in decks" :key="deck.id">
      <router-link :to="'/deck/' + deck.id">
        <span v-html="searched(deck.name)" class="name"></span>
      </router-link>
    </section>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
function escapeRegExp(string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"); // $& means the whole matched string
}
@Component({})
export default class MyDecks extends Vue {
  search = "";
  type = "mine";

  mounted() {
    console.log("mounted");
    this.$store.dispatch("getMyDecks");
    this.$store.dispatch("getFavoriteDecks");
    this.$store.commit("resetDeck");
  }

  searched(text) {
    if (!this.search) return text;
    const escaped = escapeRegExp(this.search);
    const regex = new RegExp("" + escaped + "", "i");
    return text.replace(regex, "<span>$&</span>");
  }

  get decks() {
    let decks = this.$store.state.decks;
    if (this.type === "favorites") {
      decks = this.$store.state.favoriteDecks;
    }
    if (!this.search) return decks;
    return decks.filter(deck =>
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
</style>
