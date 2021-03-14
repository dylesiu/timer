import Vue from 'vue';
import Login from "../views/Login";
import Panel from "../views/Panel";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const routes = [
  {
    path: '/login',
    component: Login
  },
  {
    path: '/',
    component: Panel,
    meta: { requiresAuth: true }
  }
];

const router = new VueRouter({
  mode: 'history',
  routes
});

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (localStorage.getItem('app_user') == null) {
      next({
        path: '/login'
      })
    } else {
      next()
    }
  } else {
    next()
  }
});

export default router;
