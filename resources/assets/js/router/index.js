import Vue from 'vue';
import VueRouter from 'vue-router';
import IndexComponent from '../components/IndexComponent.vue';
import MaterialComponent from '../components/MaterialComponent.vue';

Vue.use(VueRouter);

const router = new VueRouter({
  routes: [
    {
      path: '/',
      name: 'index',
      component: IndexComponent,
      meta: {
        requiresAuth: false,
      },
    },
    {
      path: '/material',
      name: 'material',
      component: MaterialComponent,
      meta: {
        requiresAuth: true,
      },
    },
  ],
});

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (window.Laravel.userid === '') {
      router.app.$notify.error({
        title: 'Error',
        message: 'Please login first',
      });
      next('/');
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
