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
    deck: null
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
    }
  },
  actions: {
    async getCurrentUser(context) {
      const res = await request.get("/api/user/current");
      const user = res.data.user;
      context.commit("setCurrentUser", user);
    },
    async getMyDecks(context) {
      const res = await request.get("/api/decks/mine");
      const decks = res.data.decks;
      context.commit("setMyDecks", decks);
    },
    async getDeck(context, deckid) {
      const res = await request.get(`/api/decks/${deckid}`);
      const deck = res.data.deck;
      context.commit("setDeck", deck);
    },
    async createDeck(context, deck) {
      const res = await request.post(`/api/decks`, deck);
      const newDeck = res.data.deck;
      context.commit("setDeck", newDeck);
    }
  },
  modules: {}
});
