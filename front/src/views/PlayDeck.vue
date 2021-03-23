<template>
  <div class="deck" v-if="deck">
    <router-link :to="'/deck/' + deckid">
      <i class="icon icon-chevron-left"></i>
      Back
    </router-link>
    <br />
    <br />

    <h1>Play Deck</h1>
    <p>
      The cards will be randomly selected, but there is 70% chance you get a
      card you failed, and 30% chance another card.
      <br />
      If you enable "Failed cards only" then you will only get cards you failed
      at least once in the last 3 attempts.
    </p>
    <br />
    <p>
      <at-switch :value="failedOnly" @change="onChangeFailedOnly"></at-switch>
      &nbsp;
      <b>Failed cards only</b>
    </p>
    <br />

    <h2>Do you know how to answer this question ?</h2>
    <div class="evaluation">
      <at-button
        type="success"
        size="large"
        @click="yes"
        :disabled="buttonDisabled || noMoreFailures"
      >
        Yes
      </at-button>
      <at-button
        type="error"
        size="large"
        @click="no"
        :disabled="buttonDisabled || noMoreFailures"
      >
        No
      </at-button>
    </div>
    <br />
    <at-button size="large" @click="choose">
      Next
    </at-button>
    <br />
    <br />

    <div v-if="noMoreFailures">
      <at-alert type="warning" message="No more failed cards to display">
      </at-alert>
    </div>
    <div v-else>
      <h2>
        Question
      </h2>
      <p v-if="card" class="question">{{ card.question }}</p>
      <h2>
        Answer
      </h2>
      <p v-if="card" class="answer">
        <span>
          {{ card.answer }}
        </span>
      </p>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
@Component({})
export default class PlayDeck extends Vue {
  index = null;
  failedOnly = false;
  buttonDisabled = false;
  noMoreFailures = false;

  async mounted() {
    console.log("mounted");
    await this.$store.dispatch("getDeck", this.deckid);
    this.choose();
  }

  get deckid() {
    return this.$route.params.deckid;
  }

  get deck() {
    return this.$store.state.deck;
  }

  get card() {
    if (this.index !== null) {
      return this.deck.cards[this.index];
    }
    return null;
  }

  get failures() {
    const list = [];
    this.deck.cards.forEach((card, index) => {
      if (!card.answers) return list.push(index);
      if (!card.answers.list) return list.push(index);
      let sinceFailure = 0;
      const invert = card.answers.list.reverse();
      for (let i = 0, len = invert.length; i < len; i++) {
        if (!invert[i]) {
          break;
        }
        sinceFailure++;
      }
      const isValid = sinceFailure >= 3;
      if (!isValid) {
        list.push(index);
      }
    });
    return list;
  }

  choose() {
    console.log("choose");
    const threshold = 0.7;
    const score = Math.random();
    const failedOnly = this.failedOnly;
    if (failedOnly || score < threshold) {
      this.chooseFailures();
    } else {
      this.chooseAll();
    }
  }

  chooseFailures() {
    console.log("chooseFailures");
    const max = this.failures.length;
    if (max === 0 && this.failedOnly) {
      console.log("No more failures");
      this.noMoreFailures = true;
      return;
    }
    if (max === 0 || max === 1) {
      console.log("Only one failure remaining");
      this.chooseAll();
      return;
    }
    let value = null;
    const previous = this.index;
    let index = null;
    console.log("choosing to max", max);
    do {
      value = Math.random() * max;
      index = Math.floor(value);
      index = this.failures[index];
    } while (index === previous && max > 1);
    this.index = index;
    console.log("failure choosen", index);
  }

  chooseAll() {
    console.log("chooseAll");
    const max = this.deck.cards.length;
    if (max === 0) {
      return;
    }
    let value = 0;
    const previous = this.index;
    let index = null;
    do {
      value = Math.random() * max;
      index = Math.floor(value);
    } while (index === previous && max > 1);
    this.index = index;
    console.log("choose", value, index);
    console.log("card", this.deck.cards[this.index]);
  }

  async yes() {
    const params = {
      card: this.card,
      type: "success"
    };
    this.buttonDisabled = true;
    await this.$store.dispatch("setAnswer", params);
    setTimeout(() => (this.buttonDisabled = false), 500);
    this.choose();
  }

  async no() {
    const params = {
      card: this.card,
      type: "failure"
    };
    this.buttonDisabled = true;
    await this.$store.dispatch("setAnswer", params);
    setTimeout(() => (this.buttonDisabled = false), 500);
    this.choose();
  }

  onChangeFailedOnly() {
    this.failedOnly = !this.failedOnly;
    console.log("choice", this.failedOnly);
    this.noMoreFailures = false;
    this.choose();
  }
}
</script>

<style lang="scss" scoped>
.question,
.answer {
  font-size: 20px;
  border-radius: 5px;
  border: 1px solid hsl(0, 0%, 80%);
  padding: 10px;
  padding-left: 20px;
  padding-right: 20px;
  margin-top: 20px;
  margin-bottom: 20px;
}

.answer span {
  transition: 0.3s;
  filter: blur(4px);
}

.answer:hover span {
  filter: blur(0);
}

.evaluation {
  display: flex;
  & > * {
    flex: 1;
    &:first-child {
      margin-right: 10px;
    }
  }
}
</style>
