import Vue from "vue";
import VueRouter, { RouteConfig } from "vue-router";
import Home from "../views/Home.vue";
import MyDecks from "../views/MyDecks.vue";
import Deck from "../views/Deck.vue";
import DeckCreate from "../views/DeckCreate.vue";

Vue.use(VueRouter);

const routes: Array<RouteConfig> = [
  {
    path: "/",
    name: "Home",
    component: Home
  },
  {
    path: "/my-decks",
    name: "My Decks",
    component: MyDecks
  },
  {
    path: "/deck/create",
    name: "DeckCreate",
    component: DeckCreate
  },
  {
    path: "/deck/:deckid",
    name: "Deck",
    component: Deck
  },
  {
    path: "/about",
    name: "About",
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () =>
      import(/* webpackChunkName: "about" */ "../views/About.vue")
  }
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes
});

export default router;
