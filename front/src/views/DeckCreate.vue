<template>
  <div class="deck-create">
    <h1>Create new deck</h1>
    <div class="form">
      <at-alert v-if="error" id="error" type="error" :message="error" />
      <label>Name</label>
      <at-input
        id="name"
        v-model="name"
        placeholder="Name of the deck"
      ></at-input>
      <label>Description</label>
      <at-textarea
        id="description"
        v-model="description"
        placeholder="Description of the deck"
      >
      </at-textarea>
      <at-button id="create-button" type="success" @click="createDeck"
        >Create</at-button
      >
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";

@Component({})
export default class DeckCreate extends Vue {
  name = "";
  description = "";
  error = "";

  mounted() {
    console.log("mounted");
  }

  async createDeck() {
    this.error = "";
    if (!this.name.trim()) {
      this.error = "Name can NOT be empty";
      return;
    }
    const deck = {
      name: this.name,
      description: this.description
    };
    this.$store.commit("resetDeck");
    await this.$store.dispatch("createDeck", deck);
    console.log("done", this.$store.state.deck);
    const deckid = this.$store.state.deck.id;
    this.$router.push("/deck/" + deckid);
  }
}
</script>

<style lang="scss" scoped>
h1 {
  margin-bottom: 10px;
}

.form > * {
  margin-bottom: 10px;
}
</style>
