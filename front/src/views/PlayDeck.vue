<template>
  <div class="deck" v-if="deck">
    <router-link :to="'/deck/' + deckid">
      <i class="icon icon-chevron-left"></i>
      Back
    </router-link>
    <br />
    <br />
    <h1>Play Deck</h1>
    <h2>Do you know how to answer this question ?</h2>
    <div class="evaluation">
      <at-button type="success" size="large" @click="yes">
        Yes
      </at-button>
      <at-button type="error" size="large" @click="no">
        No
      </at-button>
    </div>
    <br />
    <at-button size="large" @click="choose">
      Next
    </at-button>
    <br />
    <br />
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
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
@Component({})
export default class PlayDeck extends Vue {
  index = null;

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
    this.deck.cards.forEach((card,index) => {
      if (!card.answers) return false;
      if (!card.answers.list) return false;
      let sinceFailure = 0;
      const invert = card.answers.list.reverse();
      for (let i = 0, len = invert.length; i < len; i++) {
        if (!invert[i]) {
          break;
        }
        sinceFailure++;
      }
      console.log("------");
      console.log("invert", invert);
      console.log("sinceFailure", index, sinceFailure);
      const isValid = sinceFailure > 3;
      if (!isValid) {
        list.push(index);
      }
    });
    return list;
  }

  choose() {
    const threshold = 0.7;
    const score = Math.random();
    if (score > threshold) {
      this.chooseAll();
    } else {
      this.chooseFailures();
    }
  }

  chooseFailures() {
    const max = this.failures.length;
    if (max === 0 || max === 1) {
      this.chooseAll();
      return;
    }
    let value = null;
    const previous = this.index;
    let index = null;
    do {
      value = Math.random() * max;
      index = Math.floor(value);
      index = this.failures[index];
    } while (index === previous && max > 1);
    this.index = index;
  }

  chooseAll() {
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
    await this.$store.dispatch("setAnswer", params);
    this.choose();
  }

  async no() {
    const params = {
      card: this.card,
      type: "failure"
    };
    await this.$store.dispatch("setAnswer", params);
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
