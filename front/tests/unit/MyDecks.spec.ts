import Vue from "vue";
import VueRouter from "vue-router";
import Vuex from "vuex";
import AtComponents from "at-ui";

Vue.use(AtComponents);
Vue.use(VueRouter);

import { shallowMount, createLocalVue } from "@vue/test-utils";
import MyDecks from "@/views/MyDecks.vue";

const localVue = createLocalVue();
localVue.use(Vuex);

declare module "vue/types/vue" {
  interface Vue {
    $Notify: any;
  }
}

const $Notify = { success: jest.fn(), error: jest.fn() };

const myDecks = [
  {
    _id: 1,
    name: "some description",
    description: "some description",
    publishedDecks: []
  },
  {
    _id: 2,
    name: "some description 2",
    description: "some description 2",
    publishedDecks: [
      {
        _id: 3,
        name: "some description 3",
        description: "some description 3",
        publishedDecks: []
      }
    ]
  }
];

const favoriteDecks = [];

describe("MyDecks.vue", () => {
  let mutations;
  let actions;
  let store;

  beforeAll(() => {
    mutations = {
      async resetDeck() {
        return true;
      }
    };
    actions = {
      getMyDecks: jest.fn(),
      getFavoriteDecks: jest.fn(),
      resetDeck: jest.fn(),
      deleteDeck: jest.fn()
    };
    store = new Vuex.Store({
      state: {
        decks: myDecks,
        favoriteDecks: favoriteDecks
      },
      actions,
      mutations
    });
  });

  it("renders decks from store", () => {
    const mocks = { $Notify };
    const wrapper = shallowMount(MyDecks, { mocks, localVue, store });
    const decksDiv = wrapper.find("#decks");
    const deckList = decksDiv.findAll("section.deck");
    expect(actions.getMyDecks).toHaveBeenCalled();
    expect(actions.getFavoriteDecks).toHaveBeenCalled();
    expect(deckList.length).toEqual(store.state.decks.length);
    deckList.wrappers.forEach(async (deckWrapper, index) => {
      const deck = store.state.decks[index];
      const name = deckWrapper.find(".name");
      expect(name.text()).toMatch(deck.name);
      if (index === 1) {
        const versionText = deck.publishedDecks.length + " versions";
        expect(deckWrapper.find(".versions").text()).toMatch(versionText);
        const deleteButton = deckWrapper.find("div.delete");
        expect(deleteButton.classes("disabled")).toBe(true);
        await deckWrapper.find("div.delete").trigger("click");
        expect(actions.deleteDeck).toHaveBeenCalled();
        expect($Notify.error).toHaveBeenCalled();
      } else {
        await deckWrapper.find("div.delete").trigger("click");
        expect(actions.deleteDeck).toHaveBeenCalled();
        expect($Notify.success).toHaveBeenCalled();
      }
    });
  });
});
