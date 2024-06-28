<template>
        <q-card style="min-width: 350px">
            <q-card-section>
                <div class="text-h6">Saisir le nom du film</div>
            </q-card-section>

            <q-card-section class="q-pt-none">
                <q-input dense v-model="movieTitleChange" @change="changeMovie" autofocus />
                <div v-if="movieUpdated.id">
                <Movie :movie="movieUpdated" />
                </div>
                <div v-else>
                <Movie :movie="movieOrigin" />
                </div>
            </q-card-section>

            <q-card-actions align="right" class="text-primary">
                <q-btn flat label="Annuler" v-close-popup @click="reset"/>
                <q-btn flat label="Valider" v-close-popup @click="updateMovie"/>
            </q-card-actions>
        </q-card>
</template>

<script >
import Movie from './Movie.vue';

export default {
  name: "EditMovie",
  props: {
    movie: {
        type: Object,
        required: true
  },
    getMovieWithGenre: {
        type: Function,
        required: true
    }
  },
  components: {
    Movie,
  },
  data () {
    return {
      movieTitleChange: this.movie.name,
      movieOrigin: this.movie,
      movieUpdated: {}
    }
  },
  methods :{
    async changeMovie() {
    this.movieUpdated = await this.getMovieWithGenre(this.movieTitleChange);
    },
    updateMovie(){
      if(this.movieUpdated.id)
      {
        this.$emit('update-movie', this.movieUpdated);
      }
    },
    reset(){
     this.movieTitleChange= this.movie.name
    }
  }
}
</script>
