// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
// import Editor from 'tui-editor';
import App from './App';
import router from './router';

Vue.config.productionTip = false;
Vue.use(ElementUI);
Vue.prototype.consoleLog = (text) => {
  console.log(text);
};
Vue.prototype.alertErr = (text) => {
  alert(text);
};
Vue.prototype.getUrlParam = () => {
  const obj = {};
  const url = window.location;
  obj.hash = url.hash.replace('#', '');
  obj.param = {};
  const arr = url.search.slice(1).split('&');
  for (let i = 0; i < arr.length; i += 1) {
    const temp = arr[i].split('=');
    obj.param[temp[0]] = temp[1];
  }
  return obj;
};
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>',
});
