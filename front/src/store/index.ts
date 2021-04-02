import Vue from "vue";
import Vuex from "vuex";
import { Deck } from "../types";
import savers from "../savers/index";

const saver = savers("web");

Vue.use(Vuex);

const store = new Vuex.Store({
  state: {
    online: true,
    checkingLogStatus: false,
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
    setCheckingLogStatus(state, status) {
      state.checkingLogStatus = status;
    },
    setOnlineStatus(state, status) {
      state.online = status;
    },
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
    removeDeck(state, deck) {
      const index = state.decks.indexOf(deck);
      state.decks.splice(index, 1);
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
      context.commit("setCheckingLogStatus", true);
      try {
        const user = await saver.getCurrentUser();
        context.commit("setCurrentUser", user);
        context.commit("setLoggedStatus", true);
        localStorage.apikey = user.apikey;
      } catch (e) {
        context.commit("setLoggedStatus", false);
      }
      context.commit("setCheckedLogStatus", true);
      context.commit("setCheckingLogStatus", false);
    },
    async register(context, credentials) {
      try {
        await saver.register(credentials);
        return true;
      } catch (e) {
        return false;
      }
    },
    async login(context, credentials) {
      try {
        const user = await saver.login(credentials);
        console.log("login", user);
        this.commit("setCurrentUser", user);
        context.commit("setLoggedStatus", true);
        context.commit("setCheckedLogStatus", true);
        localStorage.apikey = user.apikey;
        return user;
      } catch (e) {
        console.log("login error", e.response);
        const message = e.response.data.message;
        return { error: true, message };
      }
    },
    async logout(context) {
      await saver.logout();
      this.commit("setCurrentUser", null);
      context.commit("setLoggedStatus", false);
      localStorage.apikey = "";
    },
    // DECKS
    async getPublicDecks(context) {
      const decks = await saver.getPublicDecks();
      context.commit("setPublicDecks", decks);
    },
    async getMyDecks(context) {
      const decks = await saver.getMyDecks();
      context.commit("setMyDecks", decks);
    },
    async getFavoriteDecks(context) {
      const decks = await saver.getFavoriteDecks();
      context.commit("setFavoriteDecks", decks);
    },
    async getDeck(context, deckid) {
      const deck = await saver.getDeck(deckid);
      context.commit("setDeck", deck);
    },
    async createDeck(context, deck) {
      const newDeck = await saver.createDeck(deck);
      context.commit("setDeck", newDeck);
    },
    async editDeck(context, data) {
      try {
        await saver.editDeck(data);
        data.deck.name = data.patch.name;
        data.deck.description = data.patch.description;
        return true;
      } catch (e) {
        console.error("editDeck error", e);
        return false;
      }
    },
    async deleteDeck(context, deck) {
      const res = await saver.deleteDeck(deck);
      context.commit("removeDeck", deck);
      return res;
    },
    async publishDeck(context, deck) {
      const published = await saver.publishDeck(deck);
      deck.publishedDecks.push(published);
      return { success: true };
    },
    async addToFavorites(context, deck) {
      await saver.addToFavorites(deck);
      deck.favorite = true;
      return { success: true };
    },
    async removeFromFavorites(context, deck) {
      await saver.removeFromFavorites(deck);
      deck.favorite = false;
      return { success: true };
    },
    // CARDS
    async createCard(context, data) {
      const newCard = await saver.createCard(data);
      data.deck.cards.push(newCard);
    },
    async editCard(context, data) {
      try {
        data.card.question = data.patch.question;
        data.card.answer = data.patch.answer;
        return true;
      } catch (e) {
        console.error("ediCard error", e);
        return false;
      }
    },
    async setAnswer(context, data) {
      await saver.setAnswer(data);
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

export default store;

function updateOnlineStatus() {
  store.commit("setOnlineStatus", navigator.onLine);
}
updateOnlineStatus();
window.addEventListener("online", updateOnlineStatus);
window.addEventListener("offline", updateOnlineStatus);
