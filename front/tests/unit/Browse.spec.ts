import Vue from "vue";
import VueRouter from "vue-router";
import Vuex from "vuex";
import AtComponents from "at-ui";

Vue.use(AtComponents);
Vue.use(VueRouter);

import { shallowMount, createLocalVue } from "@vue/test-utils";
import Browse from "@/views/Browse.vue";

const localVue = createLocalVue();
localVue.use(Vuex);

declare module "vue/types/vue" {
  interface Vue {
    $Notify: any;
  }
}

const $Notify = { success: jest.fn(), error: jest.fn() };

const publicDecks = [
  {
    _id: 1,
    name: "some description",
    description: "some description",
    version: 1
  },
  {
    _id: 2,
    name: "some description 2",
    description: "some description 2",
    version: 2
  }
];

describe("Browse.vue", () => {
  let mutations;
  let actions;
  let store;

  beforeAll(() => {
    mutations = {
      resetDeck: jest.fn()
    };
    actions = {
      getPublicDecks: jest.fn(),
      resetDeck: jest.fn()
    };
    store = new Vuex.Store({
      state: {
        publicDecks: publicDecks
      },
      actions,
      mutations
    });
  });

  it("renders decks from store", () => {
    const mocks = { $Notify };
    const wrapper = shallowMount(Browse, { mocks, localVue, store });
    expect(actions.getPublicDecks).toHaveBeenCalled();
    expect(mutations.resetDeck).toHaveBeenCalled();
    const deckList = wrapper.findAll(".my-decks .deck");
    expect(deckList.length).toEqual(store.state.publicDecks.length);
    deckList.wrappers.forEach(async (deckWrapper, index) => {
      const deck = store.state.publicDecks[index];
      const name = deckWrapper.find(".name");
      expect(name.text()).toMatch(deck.name);
      const version = deckWrapper.find(".version");
      expect(version.text()).toMatch("Version " + deck.version);
    });
  });
});
