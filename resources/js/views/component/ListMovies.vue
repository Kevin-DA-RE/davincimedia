<script setup>
import EditMovie from "./EditMovie.vue";
import Movie from "./Movie.vue";

let props = defineProps({
  movie: {
    type: Object,
    required: true,
  },
  getMovieWithGenre: {
    type: Function,
  },
});
let dialog = ref(false);
let movieOrigin = props.movie;

function updateMovie(movieupdated) {
  if (movieupdated.id) {
    movieOrigin.value = movieupdated;
  }
}
function showDialog() {
  dialog.value = true;
}
</script>

<template>
  <q-card class="my-card" style="width: 50vh">
    <q-btn label="Modifier" color="primary" @click="showDialog" />
    <Movie :movie="movieOrigin" />
    <EditMovie
      :getMovieWithGenre="getMovieWithGenre"
      :movie="movie"
      @update-movie="updateMovie"
      :dialog="dialog"
    />
  </q-card>
</template>
