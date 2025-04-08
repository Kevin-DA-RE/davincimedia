<template>
   <!--Card pour afficher les éléments-->
 <div class="q-mt-sm q-mb-sm q-pa-sm flex justify-center">
    <q-img :src="movie.url_img" style="width: 150px; height: max-content;"/>
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
