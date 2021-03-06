import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import AtComponents from "at-ui";
import "at-ui-style";
import "@/assets/global.scss";
import TextHighlight from "vue-text-highlight";
Vue.component("text-highlight", TextHighlight);

Vue.config.productionTip = false;

Vue.use(AtComponents);

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");
