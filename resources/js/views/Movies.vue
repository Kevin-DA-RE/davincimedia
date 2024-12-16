<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import Movie from "./component/Movie.vue";

const moviesList = ref([])
const genresList =ref([])
const btnMovie = ref(false)

onMounted( async() => {
      try {
              const movies = await axios
                  .get(`http://127.0.0.1:8000/api/movie/show-movie`, {
                        headers: {
                        accept: "application/json",
                        },
                  })
                  .then((movie) => movie.data)
                 
                  if (movies.length > 0) {
                        console.log("on ajoute des films");
                        moviesList.value.push(movies)
                  }
                  console.log(movies);
                  
      } catch (error) {
            console.log(error);
            
      }

      try {
             const genres = await axios
                  .get(`http://127.0.0.1:8000/api/movie/show-genre`, {
                        headers: {
                        accept: "application/json",
                        },
                  })
                  .then((genre) => genre.data)
                  console.log(genres.length);
      } catch (error) {
            console.log(error);
      }

    btnMovie.value = (moviesList.value.length < 0) ? false: true;
      console.log(btnMovie.value);
      
})

const props = defineProps({
  leftDrawerOpen: {
    type: Boolean
  },
});

</script>

<template>
  <q-page>
    <div v-if="moviesList.length > 0">
      <q-card class="my-card" style="width: 50vh">
        <Movie :movie="movie" />
      </q-card>
    </div>
    <div v-else>
      <q-btn color="primary" label="Ajouter film" v-show="btnMovie" />
    </div>
  </q-page>
  
</template>

<style>
</style>