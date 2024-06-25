import { createRouter, createWebHistory } from 'vue-router'
// import HomeView from '../views/HomeView.vue'
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component:() => import('../views/HomeView.vue')
    },
    {
      path: '/conversations',
      name: 'home',
      component:() => import('../views/ChatView.vue')
    },
    {
      path:'/conversations/:conversation_id',
      name:'conversation',
      component:() => import('../views/ChatView.vue')
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue')
    },
    {
      path: '/login',
      name: 'auth.login',
      component: () => import('../views/auth/Login.vue')
    },
    {
      path: '/register',
      name: 'auth.register',
      component: () => import('../views/auth/Register.vue')
    },
    {
      path: '/contact',
      name: 'contact',
      component: () => import('../views/Contact.vue')
    },
    {
      path: '/contact/:user_id',
      name: 'contact-detail',
      component: () => import('../views/Contact.vue')
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/Profile.vue')
    }
  ]
})

export default router
