import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";
import { Deck } from "../types";

const baseURL = process.env.VUE_APP_API_URL;

const apikey = localStorage.apikey;

let request = axios.create({
  baseURL,
  headers: {
    "X-AUTH-TOKEN": apikey
  }
});

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    checkedLogStatus: false,
    isLogged: false,
    user: null,
    decks: [] as Deck[],
    publicDecks: [],
    favoriteDecks: [],
    deck: null as Deck | null,
    card: null
  },
  mutations: {
    setCurrentUser(state, user) {
      state.user = user;
      console.log(user);
    },
    setLoggedStatus(state, isLogged) {
      state.isLogged = isLogged;
    },
    setCheckedLogStatus(state, logStatus) {
      state.checkedLogStatus = logStatus;
    },
    setMyDecks(state, decks) {
      state.decks = decks;
      console.log(decks);
    },
    setFavoriteDecks(state, decks) {
      state.favoriteDecks = decks;
      console.log(decks);
    },
    setPublicDecks(state, decks) {
      state.publicDecks = decks;
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
      try {
        const res = await request.get("/api/user/current");
        const user = res.data.user;
        context.commit("setCurrentUser", user);
        context.commit("setLoggedStatus", true);
      } catch (e) {
        context.commit("setLoggedStatus", false);
      }
      context.commit("setCheckedLogStatus", true);
    },
    async register(context, credentials) {
      try {
        await request.post("/api/user", credentials);
        return true;
      } catch (e) {
        return false;
      }
    },
    async login(context, credentials) {
      try {
        const res = await request.post(`/user/login`, credentials);
        const user = res.data.user;
        console.log("login", user);
        this.commit("setCurrentUser", user);
        context.commit("setLoggedStatus", true);
        context.commit("setCheckedLogStatus", true);
        localStorage.apikey = user.apikey;
        request = axios.create({
          baseURL,
          headers: {
            "X-AUTH-TOKEN": user.apikey
          }
        });
        return true;
      } catch (e) {
        return false;
      }
    },
    // DECKS
    async getPublicDecks(context) {
      const res = await request.get("/api/decks");
      const decks = res.data.decks;
      context.commit("setPublicDecks", decks);
    },
    async getMyDecks(context) {
      const res = await request.get("/api/decks/mine");
      const decks = res.data.decks;
      context.commit("setMyDecks", decks);
    },
    async getFavoriteDecks(context) {
      const res = await request.get("/api/decks/favorites");
      const decks = res.data.decks;
      context.commit("setFavoriteDecks", decks);
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
    async addToFavorites(context, deck) {
      const deckid = deck.id;
      const body = { deckid };
      await request.post(`/api/decks/favorites`, body);
      deck.favorite = true;
      return { success: true };
    },
    async removeFromFavorites(context, deck) {
      const deckid = deck.id;
      await request.delete(`/api/decks/favorites/${deckid}`);
      deck.favorite = false;
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
