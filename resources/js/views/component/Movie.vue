<template>
   <!--Card pour afficher les éléments-->
 <div class="q-mt-sm q-mb-sm">
  <q-card class="text-dark " style="width: 150px; height: max-content;">
    <q-img :src="movie.url_img"/>
  </q-card>
</div>
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
