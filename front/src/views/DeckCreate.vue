<template>
  <div class="deck-create">
    <h1>Create new deck</h1>
    <div class="form">
      <label>Name</label>
      <at-input v-model="name" placeholder="Please input"></at-input>
      <label>Description</label>
      <at-textarea v-model="description" placeholder="Please input...">
      </at-textarea>
      <at-button type="success" @click="createDeck">Create</at-button>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
@Component({})
export default class DeckCreate extends Vue {
  name = "";
  description = "";

  mounted() {
    console.log("mounted");
  }

  async createDeck() {
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
.form > * {
  margin-bottom: 10px;
}
</style>
