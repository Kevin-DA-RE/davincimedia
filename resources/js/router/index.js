import { createRouter, createWebHistory } from 'vue-router'
import Movies from '../views/Movies.vue';
import Series from '../views/Series.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'movies',
      component: Movies
    },
    {
    path: '/',
    name: 'series',
    component: Series
    }
  ]
})

export default router
