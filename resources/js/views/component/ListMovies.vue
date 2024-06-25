<template>
  <q-card class="my-card" style="width: 50vh;">
    <q-btn label="Modifier" color="primary" @click="promptUpdate = true" />
    <Movie :movie="movieOrigin" />


        <q-dialog v-model="promptUpdate" persistent>
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
        </q-dialog>
    </q-card>
</template>
<script >
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
    Movie
  },
  data() {
    return{
      promptUpdate: false,
      movieTitleChange: this.movie.name,
      movieOrigin: this.movie,
      movieUpdated: {}
    }

  },
  methods: {
   async changeMovie() {
    this.movieUpdated = await this.getMovieWithGenre(this.movieTitleChange);
    },
    updateMovie(){
      if(this.movieUpdated.id)
      {
        console.log("mise a jour du film");
        this.movieOrigin = this.movieUpdated;
      }
    },
    reset(){
     this.movieTitleChange= this.movie.name
    }
  }
}
</script>
