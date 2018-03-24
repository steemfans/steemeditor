import Vue from 'vue';
import Router from 'vue-router';
import IndexComponent from '../components/IndexComponent.vue';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'index',
      component: IndexComponent,
    },
  ],
});
