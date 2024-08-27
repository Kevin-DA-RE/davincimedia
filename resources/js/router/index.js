import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue';
import Upload from '../views/Upload.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Upload
    },
  ]
})

export default router
