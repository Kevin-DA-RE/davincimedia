<template>
  <q-card class="my-card" style="width: 50vh;">
    <q-btn label="Modifier" color="primary" @click="modal = true" />
    <Movie :movie="movieOrigin" />

        <q-dialog v-model="modal" persistent>
            <EditMovie :getMovieWithGenre="getMovieWithGenre" :movie="movie" @update-movie="updateMovie"/>
        </q-dialog>
    </q-card>
</template>
<script >
import EditMovie from './EditMovie.vue';
import  Movie from './Movie.vue';
export default {
  name: "ListMovies",
  props: {
    movie: {
      type: Object,
      required: true,
    },
    getMovieWithGenre: {
        type: Function
    }
  },
  components: {
    Movie,
    EditMovie
  },
  data() {
    return{
      modal: false,
      movieOrigin: this.movie,
    }

  },
  methods: {
    updateMovie(movieupdated){
        if (movieupdated.id) {
        this.movieOrigin = movieupdated;
        }
    },
    reset(){
     this.movieTitleChange= this.movie.name
    }
  }
}
</script>
