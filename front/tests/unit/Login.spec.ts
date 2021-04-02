import Vue from "vue";
import Vuex from "vuex";
import AtComponents from "at-ui";
import { mount, createLocalVue } from "@vue/test-utils";
import Login from "@/components/Login.vue";

Vue.use(AtComponents);

const localVue = createLocalVue();
localVue.use(Vuex);

describe("Browse.vue", () => {
  it("login properly", async () => {
    const callLogin = jest.fn();
    const actions = {
      async login() {
        callLogin();
        return {};
      }
    };
    const store = new Vuex.Store({
      actions
    });
    const mocks = {
      $router: {
        push: jest.fn()
      }
    };
    const wrapper = mount(Login, { mocks, localVue, store });
    await wrapper.findComponent(AtComponents.Button).trigger("click");
    expect(callLogin).toHaveBeenCalled();
    expect(mocks.$router.push).toHaveBeenCalled();
  });

  it("display an error message", async () => {
    const callLogin = jest.fn();
    const message = "some error message";
    const actions = {
      async login() {
        callLogin();
        return { error: { message } };
      }
    };
    const store = new Vuex.Store({
      actions
    });
    const wrapper = mount(Login, { localVue, store });
    await wrapper.findComponent(AtComponents.Button).trigger("click");
    expect(callLogin).toHaveBeenCalled();
    console.log("check message");
    console.log(wrapper.findAll("*"));
    expect(wrapper.findComponent(AtComponents.Alert).text()).toBe(message);
  });
});
