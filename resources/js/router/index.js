import { createRouter, createWebHistory } from 'vue-router'
import Movies from '../views/Movies.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'movies',
      component: Movies
    }
  ]
})

export default router
