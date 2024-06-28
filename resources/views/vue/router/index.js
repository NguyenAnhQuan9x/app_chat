import { createRouter, createWebHistory } from 'vue-router'
// import HomeView from '../HomeView.vue'
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component:() => import('../HomeView.vue')
    },
    {
      path: '/conversations',
      name: 'home',
      component:() => import('../ChatView.vue')
    },
    {
      path:'/conversations/:conversation_id',
      name:'conversation',
      component:() => import('../ChatView.vue')
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../AboutView.vue')
    },
    {
      path: '/login',
      name: 'auth.login',
      component: () => import('../auth/Login.vue')
    },
    {
      path: '/register',
      name: 'auth.register',
      component: () => import('../auth/Register.vue')
    },
    {
      path: '/contact',
      name: 'contact',
      component: () => import('../Contact.vue')
    },
    {
      path: '/contact/:user_id',
      name: 'contact-detail',
      component: () => import('../Contact.vue')
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../Profile.vue')
    }
  ]
})

export default router
