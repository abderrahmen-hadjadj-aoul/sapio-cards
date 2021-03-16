import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";

const request = axios.create({
  baseURL: "http://localhost:8000"
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
      context.commit("setCurrentUser", user);
    },
    // DECKS
    async getMyDecks(context) {
      const res = await request.get("/api/decks/mine");
      const decks = res.data.decks;
      context.commit("setMyDecks", decks);
    },
    async getDeck(context, deckid) {
      const res = await request.get(`/api/decks/${deckid}/cards`);
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
    }
  },
  modules: {}
});
