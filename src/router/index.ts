import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/agreement',
    name: "agreement",
    component: () => import("../views/Agreement.vue")
  },
  /*
  {
    path: '/admin',
    name: "admin",
    component: () => import("../views/Admin.vue"),
    // don't use children feature!
  },*/
  {
    path: "/admin/view-requests/:page?",
    name: "admin-view-requests",
    component: () => import("../views/AdminApplicationView.vue")
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
