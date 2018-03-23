require('./bootstrap');

import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import router from './router';
import App from './App.vue';

Vue.use(ElementUI);

Vue.config.productionTip = false;
Vue.prototype.consoleLog = (text) => {
  console.log(text);
};
Vue.prototype.alertErr = (text) => {
  alert(text);
};

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>',
});
