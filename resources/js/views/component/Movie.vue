<template>
  <!--Card pour afficher les éléments-->
    <div class="row items-start">
        <div
            v-for="genre in movie.genre"
            :key="movie.genre.id_genre"
            class="row ">
        <q-badge outline color="primary" :label="genre.name" />
    </div>
    </div>

  <q-card class="text-dark " style="width: 150px; height: max-content;">
    <q-img :src="movie.url_img"/>
  </q-card>
</template>


<script setup>
defineProps({
  movie: {
    type: Object,
    required: true,
  },
});
</script>
<style>
</style>
