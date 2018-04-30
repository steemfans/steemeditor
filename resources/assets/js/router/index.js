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
    {
      path: '/voteme',
      name: 'voteme',
      meta: {
        requiresAuth: false,
      },
    },
    // <a href="https://steemconnect.com/sign/account-witness-vote?witness=ety001&approve=1" target="_blank">Vote ME!!!</a>
  ],
});

router.beforeEach((to, from, next) => {
  if (to.name === 'voteme') {
    window.open('https://steemconnect.com/sign/account-witness-vote?witness=ety001&approve=1');
    window.location = '/';
    next(false);
  }
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
