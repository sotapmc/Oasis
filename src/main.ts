import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import "./css/vue-material.min.css";
import "./custom.less";
import "@mdi/font/css/materialdesignicons.css";
import Server from './server';
import bus from './bus';

Vue.config.productionTip = false

Vue.prototype.$server = Server;
Vue.prototype.$bus = bus;

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
