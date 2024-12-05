
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


</script>

<template>
  <q-layout view="lHh Lpr lFf" class="bg-grey-9 text-white">
    <q-header elevated class="bg-grey-6">
      <q-toolbar>
        <router-link :to="{ name: 'home' }"><q-btn label="Accueil" /></router-link>
        <router-link :to="{ name: 'movies' }" ><q-btn label="Films" v-show="btnMovie"/></router-link>
        <q-toolbar-title>
          Bienvenue dans votre bibliotèque DaVinciMedia !
        </q-toolbar-title>
      </q-toolbar>
    </q-header>    
    <div class="container">
        <router-view></router-view>
    </div>
  </q-layout>
</template>

<style></style>
