import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import AtComponents from "at-ui";
import "at-ui-style";

//import Login from "./src/components/Login.vue";

//Vue.component("Login", Login);

Vue.config.productionTip = false;

Vue.use(AtComponents);

store.dispatch("getCurrentUser");

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");
