import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";

const request = axios.create({
  baseURL: "https://localhost:8000",
  headers: {
    "X-AUTH-TOKEN": "TEST-API-KEY"
  }
});

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: null,
    decks: [],
    deck: null,
    card: null
  },
  mutations: {
    setCurrentUser(state, user) {
      state.user = user;
      console.log(user);
    },
    setMyDecks(state, decks) {
      state.decks = decks;
      console.log(decks);
    },
    setDeck(state, deck) {
      state.deck = deck;
      console.log(deck);
    },
    resetDeck(state) {
      state.deck = null;
    },
    setCard(state, card) {
      state.card = card;
    }
  },
  actions: {
    // USER
    async getCurrentUser(context) {
      const res = await request.get("/api/user/current");
      const user = res.data.user;
      console.log("user", user);
      context.commit("setCurrentUser", user);
    },
    // DECKS
    async getMyDecks(context) {
      const res = await request.get("/api/decks/mine");
      const decks = res.data.decks;
      context.commit("setMyDecks", decks);
    },
    async getDeck(context, deckid) {
      const res = await request.get(`/api/decks/${deckid}/cards?published=1`);
      const deck = res.data.deck;
      context.commit("setDeck", deck);
    },
    async createDeck(context, deck) {
      const res = await request.post(`/api/decks`, deck);
      const newDeck = res.data.deck;
      context.commit("setDeck", newDeck);
    },
    async editDeck(context, data) {
      const deckid = data.deck.id;
      const patch = data.patch;
      try {
        await request.patch(`/api/decks/${deckid}`, patch);
        data.deck.name = data.patch.name;
        data.deck.description = data.patch.description;
        return true;
      } catch (e) {
        console.error("editDeck error", e);
        return false;
      }
    },
    async publishDeck(context, deck) {
      const deckid = deck.id;
      const published = await request.post(`/api/decks/${deckid}/published`);
      deck.publishedDecks.push(published);
      return { success: true };
    },
    // CARDS
    async createCard(context, data) {
      const deckid = data.deck.id;
      const card = data.card;
      const res = await request.post(`/api/decks/${deckid}/cards`, card);
      const newCard = res.data.card;
      data.deck.cards.push(newCard);
    },
    async editCard(context, data) {
      const cardid = data.card.id;
      const patch = data.patch;
      try {
        await request.patch(`/api/cards/${cardid}`, patch);
        data.card.question = data.patch.question;
        data.card.answer = data.patch.answer;
        return true;
      } catch (e) {
        console.error("ediCard error", e);
        return false;
      }
    },
    async setAnswer(context, data) {
      const cardid = data.card.id;
      const body = {
        type: data.type
      };
      await request.post(`/api/cards/${cardid}/answer`, body);
      if (!data.card.answers) {
        data.card.answers = {
          success: 0,
          failure: 0
        };
      }
      if (data.type === "success") {
        data.card.answers.success++;
      }
      if (data.type === "failure") {
        data.card.answers.failure++;
      }
    }
  },
  modules: {}
});
