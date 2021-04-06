<template>
  <div v-if="deck" class="deck" :data-test-id="deck.id">
    <div class="buttons">
      <at-button
        v-if="editable"
        id="update-deck-button"
        type="primary"
        icon="icon-edit"
        @click="showModalDeck"
      >
        Update deck
      </at-button>
      <at-button
        v-if="editable"
        type="primary"
        icon="icon-navigation"
        :disabled="publishDisabled"
        @click="publish"
      >
        Publish
      </at-button>
      <router-link :to="'/play-deck/' + deckid">
        <at-button type="success" icon="icon-play">
          Play Deck
        </at-button>
      </router-link>
      <at-button
        v-if="enableFavoriteButton"
        :disabled="disableFavorite"
        :type="typeFavorite"
        @click="toogleFavorites"
      >
        <i class="icon icon-star-on"></i>
        &nbsp;&nbsp;
        <span v-if="deck.favorite">
          Remove from favorite
        </span>
        <span v-else>
          Add to favorite
        </span>
      </at-button>
    </div>

    <h1>Deck: {{ deck.name }}</h1>
    <p>Failures: {{ failures }} / {{ total }} - {{ percentage }}%</p>
    <p>{{ deck.description }}</p>

    <at-switch :value="failedOnly" @change="onChangeFailedOnly"></at-switch>
    Failed only
    <br />
    <br />

    <form v-if="editable" class="create-card">
      <h2>Create new card</h2>
      <at-alert
        v-if="errorCard"
        id="error-card"
        type="error"
        :message="errorCard"
      />
      <at-textarea
        id="question"
        v-model="question"
        placeholder="Question"
      ></at-textarea>
      <at-textarea
        id="answer"
        v-model="answer"
        placeholder="Answer"
      ></at-textarea>
      <at-button id="create-card-button" type="primary" @click="createCard"
        >Create card</at-button
      >
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
      <tr
        v-for="(card, index) in cards"
        :key="card.id"
        :data-test-cardid="card.id"
      >
        <td class="index">#{{ index + 1 }}</td>
        <td class="question">{{ card.question }}</td>
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
              v-for="(item, indexAnswer) in last(card)"
              :key="indexAnswer"
              class="dot"
              :class="{ green: item }"
            ></span>
          </div>
        </td>
        <td>
          <at-button
            class="edit-card-button"
            icon="icon-edit"
            title="Edit"
            @click="showModalCard(card)"
          >
          </at-button>
        </td>
      </tr>
    </table>

    <div class="bottom"></div>

    <at-modal
      id="update-modal"
      v-model="showModalDeckStatus"
      title="Edit deck"
      @on-confirm="editDeck"
      @on-cancel="showModalDeckStatus = false"
    >
      <at-alert
        v-if="errorDeck"
        id="deck-error"
        type="error"
        :message="errorDeck"
      />
      <form class="edit-card">
        <label>Name</label>
        <at-input
          id="name"
          v-model="editDeckValue.name"
          placeholder="Name of the deck"
        >
        </at-input>
        <label>Description</label>
        <at-textarea
          id="description"
          v-model="editDeckValue.description"
          placeholder="Description of the deck"
        >
        </at-textarea>
      </form>
      <div slot="footer">
        <at-button id="edit-deck-ok-button" type="primary" @click="editDeck">
          OK
        </at-button>
        <at-button @click="showModalDeckStatus = false">Cancel</at-button>
      </div>
    </at-modal>

    <at-modal
      v-model="showModalCardStatus"
      title="Edit card"
      @on-confirm="editCard"
      @on-cancel="showModalCard = false"
    >
      <at-alert
        v-if="errorCardModal"
        id="error-edit-card-modal"
        type="error"
        :message="errorCardModal"
      />
      <form class="edit-card">
        <at-textarea
          id="modal-question"
          v-model="editCardValue.question"
          placeholder="Question"
        >
        </at-textarea>
        <at-textarea
          id="modal-answer"
          v-model="editCardValue.answer"
          placeholder="Answer"
        >
        </at-textarea>
      </form>
      <div slot="footer">
        <at-button id="edit-card-ok-button" type="primary" @click="editCard">
          OK
        </at-button>
        <at-button @click="showModalCardStatus = false">Cancel</at-button>
      </div>
    </at-modal>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import { Card } from "../types";

@Component({})
export default class Deck extends Vue {
  question = "";
  answer = "";
  errorDeck = "";
  errorCard = "";
  errorCardModal = "";

  showModalCardStatus = false;
  showModalDeckStatus = false;

  card: Card | null = null;

  editCardValue = { question: "", answer: "" };
  editDeckValue = { name: "", description: "" };

  publishDisabled = false;
  failedOnly = false;

  disableFavorite = false;

  mounted() {
    console.log("mounted");
    this.$store.dispatch("getDeck", this.deckid);
  }

  get editable() {
    if (!this.deck) return null;
    return !this.deck.published;
  }

  get deckid() {
    return this.$route.params.deckid;
  }

  get deck() {
    return this.$store.state.deck;
  }

  get cards() {
    if (this.failedOnly) {
      return this.failed;
    }
    return this.deck.cards;
  }

  get failures() {
    if (!this.deck) return null;
    if (!this.deck.cards) return null;
    return this.deck.cards.filter((item: Card) => {
      if (!item.answers) return;
      if (!item.answers.list) return;
      const last = item.answers.list.length - 1;
      return !item.answers.list[last];
    }).length;
  }

  get failed() {
    return this.deck.cards.filter((item: Card) => {
      if (!item.answers) return true;
      if (!item.answers.list) return true;
      const list = item.answers.list;
      let score = 0;
      const last = item.answers.list.length - 1;
      if (list[last]) score++;
      if (list[last - 1]) score++;
      if (list[last - 2]) score++;
      return score < 3;
    });
  }

  get total() {
    if (!this.deck) return null;
    if (!this.deck.cards) return null;
    return this.deck.cards.length;
  }

  get percentage() {
    let p = this.failures / this.total;
    p = 100 * p;
    p = Math.ceil(p);
    return p;
  }

  get typeFavorite() {
    if (this.deck.favorite) {
      return "warning";
    }
    return "";
  }

  get enableFavoriteButton() {
    const user = this.$store.state.user;
    if (this.deck.owner.id === user.id) {
      return false;
    }
    return true;
  }

  async createCard() {
    this.errorCard = "";
    if (!this.question) {
      this.errorCard += "Question can NOT be empty.";
    }
    if (!this.answer) {
      this.errorCard += " Answer can NOT be empty.";
    }
    if (this.errorCard) return;
    const card = {
      question: this.question,
      answer: this.answer
    };
    const data = {
      deck: this.deck,
      card
    };
    await this.$store.dispatch("createCard", data);
    this.question = "";
    this.answer = "";
  }

  showModalDeck() {
    this.showModalDeckStatus = true;
    this.editDeckValue.name = this.deck.name;
    this.editDeckValue.description = this.deck.description;
  }

  async editDeck() {
    console.log("edit deck", this.deck);
    this.errorDeck = "";
    if (!this.editDeckValue.name.trim()) {
      this.errorDeck = "Name can NOT be empty";
      console.error(this.error);
      return;
    }
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

  showModalCard(card: Card) {
    this.showModalCardStatus = true;
    this.card = card;
    this.editCardValue.question = card.question;
    this.editCardValue.answer = card.answer;
  }

  async editCard() {
    console.log("edit card", this.card);
    this.errorCardModal = "";
    if (!this.editCardValue.question)
      this.errorCardModal += "Question can NOT be empty.";
    if (!this.editCardValue.answer)
      this.errorCardModal += "Answer can NOT be empty";
    if (this.errorCardModal) return;
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
      //this.showModalCard = false;
    } else {
      this.$Notify({
        title: "Card update",
        message: "An error occured while updating !",
        type: "error"
      });
    }
  }

  percentageCard(card: Card) {
    const total = card.answers.failure + card.answers.success;
    let p = card.answers.failure / total;
    p = 100 * p;
    p = Math.ceil(p);
    return p;
  }

  last(card: Card) {
    if (!card.answers) return;
    if (!card.answers.list) return;
    const list = card.answers.list.slice().reverse();
    return list.slice(0, 3);
  }

  async publish() {
    console.log("Publish");
    this.publishDisabled = true;
    const res = await this.$store.dispatch("publishDeck", this.deck);
    this.publishDisabled = false;
    if (res.success) {
      this.$Notify.success({
        title: "Published",
        message: "Deck successfully published"
      });
    }
  }

  onChangeFailedOnly() {
    this.failedOnly = !this.failedOnly;
  }

  toogleFavorites() {
    if (this.deck.favorite) {
      this.removeFromFavorites();
    } else {
      this.addToFavorites();
    }
  }

  async addToFavorites() {
    this.disableFavorite = true;
    await this.$store.dispatch("addToFavorites", this.deck);
    this.disableFavorite = false;
  }

  async removeFromFavorites() {
    this.disableFavorite = true;
    await this.$store.dispatch("removeFromFavorites", this.deck);
    this.disableFavorite = false;
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
  background-color: #e33410;
  margin-right: 5px;
  &.green {
    background-color: #39cc4a;
  }
}
</style>
