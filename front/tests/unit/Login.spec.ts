import Vue from "vue";
import Vuex from "vuex";
import AtComponents from "at-ui";
import { mount, createLocalVue } from "@vue/test-utils";
import Login from "@/components/Login.vue";

Vue.use(AtComponents);

const localVue = createLocalVue();
localVue.use(Vuex);

declare module "vue/types/vue" {
  interface Vue {
    error: string;
  }
}

describe("Login.vue", () => {
  it("login properly", async () => {
    const loginButton = jest.fn();
    const actions = {
      async login() {
        loginButton();
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
    expect(loginButton).toHaveBeenCalled();
    expect(mocks.$router.push).toHaveBeenCalled();
    expect(wrapper.findComponent(AtComponents.Alert).exists()).toBe(false);
  });

  it("display an error message", async () => {
    const message = "some error message";
    const loginButton = jest.fn();
    const actions = {
      async login() {
        loginButton();
        return { error: true, message };
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
    expect(loginButton).toHaveBeenCalled();
    await Vue.nextTick();
    expect(wrapper.vm.error).toBe(message);
    const alertWrapper = wrapper.findComponent(AtComponents.Alert);
    expect(alertWrapper.text()).toBe(message);
  });
});
