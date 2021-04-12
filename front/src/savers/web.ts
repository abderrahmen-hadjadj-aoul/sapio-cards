import axios from "axios";
import Saver from "./saver";
const baseURL = process.env.VUE_APP_API_URL;
const apikeyTest = process.env.VUE_APP_API_KEY_TEST;

let apikey = localStorage.apikey;
if (apikeyTest) {
  console.log(
    "VUE_APP_API_KEY_TEST detected, setting defautl apikey:",
    apikeyTest
  );
  apikey = apikeyTest;
}
const headers = {};

if (apikey) {
  headers["X-AUTH-TOKEN"] = apikey;
}
let request = axios.create({
  baseURL,
  headers
});

function setHeader(user) {
  const newHeader = {};
  if (user && user.apikey) {
    newHeader["X-AUTH-TOKEN"] = user.apikey;
  }
  request = axios.create({
    baseURL,
    headers: newHeader
  });
}

export default class WebSaver extends Saver {
  async getCurrentUser() {
    const res = await request.get("/user/current");
    const user = res.data.user;
    setHeader(user);
    return user;
  }

  async register(credentials) {
    await request.post("/api/user", credentials);
  }

  async login(credentials) {
    const res = await request.post(`/user/login`, credentials);
    const user = res.data.user;
    setHeader(user);
    return user;
  }

  async logout() {
    await request.get("/api-logout");
    setHeader({});
  }

  async getPublicDecks() {
    const res = await request.get("/api/decks");
    const decks = res.data.decks;
    return decks;
  }

  async getMyDecks() {
    const res = await request.get("/api/decks/mine");
    const decks = res.data.decks;
    return decks;
  }

  async getFavoriteDecks() {
    const res = await request.get("/api/decks/favorites");
    const decks = res.data.decks;
    return decks;
  }

  async getDeck(deckid) {
    const res = await request.get(`/api/decks/${deckid}/cards?published=1`);
    const deck = res.data.deck;
    return deck;
  }

  async createDeck(deck) {
    const res = await request.post(`/api/decks`, deck);
    const newDeck = res.data.deck;
    return newDeck;
  }

  async editDeck(data) {
    const deckid = data.deck.id;
    const patch = data.patch;
    await request.patch(`/api/decks/${deckid}`, patch);
  }

  async deleteDeck(deck) {
    const deckid = deck.id;
    await request.delete(`/api/decks/${deckid}`);
  }

  async publishDeck(deck) {
    const deckid = deck.id;
    const res = await request.post(`/api/decks/${deckid}/published`);
    const published = res.data.deck;
    return published;
  }

  async addToFavorites(deck) {
    const deckid = deck.id;
    const body = { deckid };
    await request.post(`/api/decks/favorites`, body);
  }

  async removeFromFavorites(deck) {
    const deckid = deck.id;
    await request.delete(`/api/decks/favorites/${deckid}`);
  }

  async createCard(data) {
    const deckid = data.deck.id;
    const card = data.card;
    const res = await request.post(`/api/decks/${deckid}/cards`, card);
    const newCard = res.data.card;
    return newCard;
  }

  async editCard(data) {
    const cardid = data.card.id;
    const patch = data.patch;
    await request.patch(`/api/cards/${cardid}`, patch);
  }

  async setAnswer(data) {
    const cardid = data.card.id;
    const body = {
      type: data.type
    };
    await request.post(`/api/cards/${cardid}/answer`, body);
  }
}
