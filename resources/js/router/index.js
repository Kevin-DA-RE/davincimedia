import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue';
import Movies from '../views/Movies.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    /*
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/movies',
      name: 'movies',
      component: Movies
    },
    */
    {
      path: '/',
      name: 'movies',
      component: Movies
    }
  ]
})

export default router
