<template>
  <!--Card pour afficher les éléments-->
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
