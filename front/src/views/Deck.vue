<template>
  <div class="deck" v-if="deck">
    <div class="buttons">
      <at-button type="primary" @click="showModalDeck" icon="icon-edit">
        Update deck
      </at-button>
      <router-link :to="'/play-deck/' + deckid">
        <at-button type="success" icon="icon-play">
          Play Deck
        </at-button>
      </router-link>
    </div>

    <h1>Deck: {{ deck.name }}</h1>
    <p>Failures: {{ failures }} / {{ total }} - {{ percentage }}%</p>
    <p>{{ deck.description }}</p>

    <form class="create-card">
      <h2>Create new card</h2>
      <at-textarea v-model="question" placeholder="Question"></at-textarea>
      <at-textarea v-model="answer" placeholder="Answer"></at-textarea>
      <at-button type="primary" @click="createCard">Create card</at-button>
    </form>

    <table class="cards">
      <tr>
        <th>#</th>
        <th>Question</th>
        <th>Answer</th>
        <th>Failures</th>
        <th>Last</th>
        <th></th>
      </tr>
      <tr v-for="(card, index) in deck.cards" :key="card.id">
        <td>#{{ index }}</td>
        <td>{{ card.question }}</td>
        <td class="answer">
          <span class="blur">
            {{ card.answer }}
          </span>
        </td>
        <td>
          <div v-if="card.answers">
            {{ card.answers.failure }} /
            {{ card.answers.success + card.answers.failure }} - 
            {{ percentageCard(card) }}%
          </div>
          <div v-else>
            ?
          </div>
        </td>
        <td class="td-last">
          <div class="last">
            <span
              v-for="(item, index) in last(card)"
              :key="index"
              class="dot"
              :class="{ green: item }"
            ></span>
          </div>
        </td>
        <td>
          <at-button icon="icon-edit" title="Edit" @click="showModalCard(card)">
          </at-button>
        </td>
      </tr>
    </table>

    <div class="bottom"></div>

    <at-modal
      v-model="showModalDeckStatus"
      title="Edit deck"
      @on-confirm="editDeck"
      @on-cancel="showModalDeckStatus = false"
    >
      <form class="edit-card">
        <label>Name</label>
        <at-input v-model="editDeckValue.name" placeholder="Name of the deck">
        </at-input>
        <label>Description</label>
        <at-textarea
          v-model="editDeckValue.description"
          placeholder="Description of the deck"
        >
        </at-textarea>
      </form>
    </at-modal>

    <at-modal
      v-model="showModalCardStatus"
      title="Edit card"
      @on-confirm="editCard"
      @on-cancel="showModal = false"
    >
      <form class="edit-card">
        <at-textarea v-model="editCardValue.question" placeholder="Question">
        </at-textarea>
        <at-textarea v-model="editCardValue.answer" placeholder="Answer">
        </at-textarea>
      </form>
    </at-modal>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
@Component({})
export default class Deck extends Vue {
  question = "";
  answer = "";

  showModalCardStatus = false;
  showModalDeckStatus = false;

  card = null;

  editCardValue = { question: "", answer: "" };
  editDeckValue = { name: "", description: "" };

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

  get failures() {
    return this.deck.cards.filter(item => {
      if (!item.answers) return;
      if (!item.answers.list) return;
      const last = item.answers.list.length - 1;
      return item.answers.list[last];
    }).length;
  }

  get total() {
    return this.deck.cards.length;
  }

  get percentage() {
    let p = this.failures / this.total;
    p = 100 * p;
    p = Math.ceil(p);
    return p;
  }

  async createCard() {
    const card = {
      question: this.question,
      answer: this.answer
    };
    const data = {
      deck: this.deck,
      card
    };
    await this.$store.dispatch("createCard", data);
  }

  showModalDeck() {
    this.showModalDeckStatus = true;
    this.editDeckValue.name = this.deck.name;
    this.editDeckValue.description = this.deck.description;
  }

  async editDeck() {
    console.log("edit deck", this.deck);
    const patch = {
      name: this.editDeckValue.name,
      description: this.editDeckValue.description
    };
    const data = {
      deck: this.deck,
      patch
    };
    const res = await this.$store.dispatch("editDeck", data);
    console.log("res", res);
    if (res) {
      this.$Notify({
        title: "Deck update",
        message: "Deck updated successfully",
        type: "success"
      });
    } else {
      this.$Notify({
        title: "Deck update",
        message: "An error occured while updating !",
        type: "error"
      });
    }
  }

  showModalCard(card) {
    this.showModalCardStatus = true;
    this.card = card;
    this.editCardValue.question = card.question;
    this.editCardValue.answer = card.answer;
  }

  async editCard() {
    console.log("edit card", this.card);
    const patch = {
      question: this.editCardValue.question,
      answer: this.editCardValue.answer
    };
    const data = {
      card: this.card,
      patch
    };
    const res = await this.$store.dispatch("editCard", data);
    console.log("res", res);
    if (res) {
      this.$Notify({
        title: "Card update",
        message: "Card updated successfully",
        type: "success"
      });
    } else {
      this.$Notify({
        title: "Card update",
        message: "An error occured while updating !",
        type: "error"
      });
    }
  }

  percentageCard(card) {
    const total = card.answers.failure + card.answers.success;
    let p = card.answers.failure / total;
    p = 100 * p;
    p = Math.ceil(p);
    return p;
  }

  last(card) {
    if (!card.answers) return;
    if (!card.answers.list) return;
    const list = card.answers.list.slice().reverse();
    return list.slice(0, 5);
  }
}
</script>

<style lang="scss" scoped>
.buttons {
  margin-bottom: 10px;
  & > * {
    margin-right: 10px;
  }
}

form.create-card {
  border-radius: 5px;
  border: 1px solid hsl(0, 0%, 85%);
  padding: 10px;
  margin-top: 10px;
  margin-bottom: 10px;
}

form > * {
  margin-bottom: 10px;
}

table.cards {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 50px;
}

th,
td {
  padding: 5px;
  border: 1px solid hsl(0, 0%, 90%);
}

.answer {
  .blur {
    filter: blur(4px);
    transition: 0.3s;
  }
  &:hover > .blur {
    filter: blur(0);
  }
}

.bottom {
  height: 10px;
}

.td-last {
}

.last {
  display: flex;
  justify-content: center;
}

.dot {
  display: block;
  width: 10px;
  height: 10px;
  border-radius: 10px;
  background-color: #E33410;
  margin-right: 5px;
  &.green {
    background-color: #39CC4A;
  }
}
</style>
