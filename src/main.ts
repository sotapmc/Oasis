import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import "./css/vue-material.min.css";
import "./css/default.css";
import "./custom.less";
import "@mdi/font/css/materialdesignicons.css";
import Server from './server';

Vue.config.productionTip = false

Vue.prototype.$server = Server;

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
