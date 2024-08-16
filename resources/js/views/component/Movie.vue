<template>
  <!--Card pour afficher les éléments-->
  <q-card class="my-card" style="width: 50vh">
    <div v-for="genre in props.movie.genre_name" :key="genre.id">
      <q-badge outline color="primary" :label="genre.name" />
    </div>
    <q-img :src="props.movie.url_img" id="qImg">
      <div class="absolute-top text-h6">
        {{ props.movie.name }}
      </div>
    </q-img>
    <q-card-section>
      {{ props.movie.synopsis }}
    </q-card-section>
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
