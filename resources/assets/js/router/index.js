import Vue from 'vue';
import Router from 'vue-router';
import IndexComponent from '../components/IndexComponent.vue';
import MaterialComponent from '../components/MaterialComponent.vue';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'index',
      component: IndexComponent,
    },
    {
      path: '/material',
      name: 'material',
      component: MaterialComponent,
      beforeEnter: (to, from, next) => {
        if (window.Laravel.userid) {
          next();
        } else {
          window.mainVue.$notify.error({
            title: 'Error',
            message: 'Please Login first',
          });
          next({
            path: '/',
          });
        }
      },
    },
  ],
});
