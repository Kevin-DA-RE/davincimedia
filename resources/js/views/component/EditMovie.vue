<script setup>
import Movie from "./Movie.vue";

let props = defineProps({
  movie: {
    type: Object,
    required: true,
  },
  dialog: {
    type: Boolean,
    required: true,
  },
  getMovieWithGenre: {
    type: Function,
    required: true,
  },
});

let movieTitleChange = ref(props.movie.name);
let movieOrigin = ref(props.movie);
let movieUpdated = ref({});
let dialog = ref(props.dialog);

async function changeMovie() {
  movieUpdated = await props.getMovieWithGenre(movieTitleChange);
}
function updateMovie() {
  if (movieUpdated.id) {
    this.$emit("update-movie", movieUpdated);
  }
}
function reset() {
  movieTitleChange = props.movie.name;
}
</script>

<template>
  <q-dialog v-model="dialog" persistent>
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
        <q-btn flat label="Annuler" v-close-popup @click="reset" />
        <q-btn flat label="Valider" v-close-popup @click="updateMovie" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
