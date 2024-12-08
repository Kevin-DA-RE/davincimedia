<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const movies = ref([])
const genres = ref([])

onMounted( async() => {
  movies.value = await axios
  .get(`http://127.0.0.1:8000/api/movie/show-movie`, {
        headers: {
        accept: "application/json",
      },
    })
    .then((movie) => movie.data)
    .catch((error) =>
      console.log(`Erreur lors de la récupération de datas sur les film \n ${error}`)
    );

  genres.value = await axios
  .get(`http://127.0.0.1:8000/api/movie/show-genre`, {
        headers: {
        accept: "application/json",
      },
    })
    .then((genre) => genre.data)
    .catch((error) =>
      console.log(`Erreur lors de la récupération de datas sur les film \n ${error}`)
    );

console.log(movies.value);
console.log(genres.value);


})

const props = defineProps({
  leftDrawerOpen: {
    type: Boolean
  },
});

</script>
<template>
  <div v-if="movies.length > 0">
    <q-drawer class="bg-grey-7 text-white" show-if-above v-model="props.leftDrawerOpen" side="left" bordered>
    <div v-for="genre in genres" :key="genre.id">
      <h2>{{ genre.name }}</h2>
    </div>
    </q-drawer>
  </div>
  <div v-else>
    <q-btn color="primary" label="Ajouter film" />
  </div>
  


</template>
<style>
</style>