<script setup>
import axios from "axios";
import { onBeforeMount, ref } from "vue";

const btnMovie = ref(false);


onBeforeMount( async() => {
  const movies = await axios
  .get(`http://127.0.0.1:8000/api/movie/show-movie`, {
        headers: {
        accept: "application/json",
      },
    })
    .then((movie) => movie.data)
    .catch((error) =>
      console.log(`Erreur lors de la récupération de datas sur les film \n ${error}`)
    );

    btnMovie.value = (movies.length === 0) ? false: true;

})

const props = defineProps({
  leftDrawerOpen: {
    type: Boolean
  },
});

</script>
<template>

  <q-drawer class="bg-grey-7 text-white" show-if-above v-model="props.leftDrawerOpen" side="left" bordered>
    <h3>Welcome to Movies</h3>
  </q-drawer>

</template>
<style>
</style>