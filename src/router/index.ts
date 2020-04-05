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
  {
    path: '/admin',
    name: "admin-router",
    redirect: {
      name: "admin-panel"
    },
    component: () => import("../views/AdminRouter.vue"),
    children: [
      {
        name: 'admin-panel',
        path: 'panel',
        component: () => import("../views/Admin.vue")
      },
      {
        name: "admin-view-requests",
        path: "view-requests/:page?",
        component: () => import("../views/AdminApplicationView.vue")
      },
      {
        name: "admin-requests-review",
        path: "review/:id",
        component: () => import("../views/AdminReviewer.vue")
      }
    ]
  },

]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
