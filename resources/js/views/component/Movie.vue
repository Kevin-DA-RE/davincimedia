<template>
  <!--Card pour afficher les éléments-->
  <q-card class="text-dark " style="width: 150px; height: max-content;">
    <q-img :src="props.movie.url_img" id="qImg"/>
  </q-card>
</template>


<script setup>
const props = defineProps({
  movie: {
    type: Object,
    required: true,
  },
});
</script>
<style>
</style>
