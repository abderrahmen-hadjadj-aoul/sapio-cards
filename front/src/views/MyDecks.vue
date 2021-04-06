<template>
  <div class="my-decks">
    <router-link class="button" to="/deck/create">
      <at-button id="create-new-deck-button" type="primary" icon="icon-plus">
        Create new deck
      </at-button>
    </router-link>

    <h1 class="title">My Decks</h1>

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

    <div id="decks">
      <section
        v-for="deck in decks"
        :key="deck.id"
        :data-test-id="deck.id"
        class="deck"
      >
        <router-link :to="'/deck/' + deck.id">
          <span class="name">
            <text-highlight :queries="search">{{ deck.name }}</text-highlight>
          </span>
          <span v-if="deck.publishedDecks.length > 0" class="versions">
            {{ deck.publishedDecks.length }} versions
          </span>
        </router-link>
        <div
          class="delete"
          :class="{ disabled: deck.publishedDecks.length > 0 }"
          @click="deleteDeck(deck)"
        >
          <i class="icon icon-trash"></i>
        </div>
      </section>
    </div>
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
  type = "mine";

  async mounted() {
    const mine = this.$store.dispatch("getMyDecks");
    const fav = this.$store.dispatch("getFavoriteDecks");
    this.$store.commit("resetDeck");
  }

  searched(text: string) {
    if (!this.search) return text;
    const escaped = escapeRegExp(this.search);
    const regex = new RegExp("" + escaped + "", "i");
    return text.replace(regex, "<span>$&</span>");
  }

  get queries() {
    if (this.search) return [this.search];
    return [];
  }

  get decks() {
    let decks = this.$store.state.decks;
    if (this.type === "favorites") {
      decks = this.$store.state.favoriteDecks;
    }
    if (!this.search) return decks;
    return decks.filter((deck: Deck) =>
      deck.name.toLowerCase().includes(this.search.toLowerCase())
    );
  }

  async deleteDeck(deck: Deck) {
    if (deck.publishedDecks.length > 0) {
      this.$Notify.error({
        title: "Deletion",
        message: "You can't delete a deck that has been publised."
      });
      return;
    }
    try {
      const res = await this.$store.dispatch("deleteDeck", deck);
      this.$Notify.success({
        title: "Deletion",
        message: `Deck ${deck.name} deleted`
      });
    } catch (e) {
      console.error(e);
      const data = e.response.data;
      const message = data.message;
      console.log(data);
      this.$Notify.error({
        title: "Deletion",
        message: `Error during deck deletion ${deck.name}\n` + message
      });
    }
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
  display: flex;
  align-items: center;
  a {
    flex: 1;
    transition: 0.3s;
    border-radius: 5px;
    border: 1px solid hsl(0, 0%, 80%);
    display: block;
    padding: 10px;
    transition: 0.3s;
  }
  a:hover {
    border-color: $hover;
    background-color: $hover-bg;
    color: $hover;
  }
  .delete {
    color: red;
    padding: 10px;
    padding-left: 15px;
    padding-right: 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
    margin-left: 10px;
    &.disabled {
      color: gray;
    }
    &:hover {
      color: white;
      background-color: red;
      &.disabled {
        background-color: gray;
      }
    }
  }
}

.count {
  margin-top: 5px;
}

.versions {
  display: inline-block;
  margin-left: 20px;
  border: 1px solid hsl(0, 0%, 70%);
  padding: 3px;
  padding-left: 7px;
  padding-right: 7px;
  border-radius: 5px;
}
</style>
