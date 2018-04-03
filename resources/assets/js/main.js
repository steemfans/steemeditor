import Vue from 'vue';
import ElementUI from 'element-ui';
import Sc2 from 'sc2-sdk';
import 'element-ui/lib/theme-chalk/index.css';
import router from './router';
import App from './App.vue';
import store from './store';

// window.axios = require('axios');
//
// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
//
// /**
//  * Next we will register the CSRF Token as a common header with Axios so that
//  * all outgoing HTTP requests automatically have it attached. This is just
//  * a simple convenience so we don't have to attach every token manually.
//  */
//
// const token = document.head.querySelector('meta[name="csrf-token"]');
//
// if (token) {
//   window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
// } else {
//   console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
// }

Vue.use(ElementUI);

window.sc = Sc2.Initialize(window.Laravel.sc2);
window.debugEditor = !(process.env.NODE_ENV === 'production');
window.consoleLog = (resultArrOrStr, msgType = 'debug') => {
  if (msgType === 'msg'
    || (msgType === 'debug' && window.debugEditor === true)) {
    // eslint-disable-next-line
    console.log(resultArrOrStr);
  }
};

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  mounted() {
    if (window.Laravel.accessToken) {
      window.sc.setAccessToken(window.Laravel.accessToken);
      this.$store.commit('logStatus', true);
      this.$store.commit('userInfo', window.Laravel.userInfo || {});
    } else {
      this.$store.commit('logStatus', false);
      this.$store.commit('userInfo', {});
    }
  },
  components: { App },
  template: '<App/>',
});
