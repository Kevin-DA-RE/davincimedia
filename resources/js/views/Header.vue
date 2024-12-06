
<template>
  <q-layout view="lHh Lpr lFf" class="bg-grey-9">
    <q-header elevated class="bg-grey-6">
      <q-toolbar class="text-white">
        <q-tabs align="left">
          <q-route-tab :to="{ name: 'home' }" label="Accueil" />
          <q-route-tab :to="{ name: 'movies' }" label="Films" @click="emitToggleLeftDrawer"/>
        </q-tabs>        
        <q-toolbar-title>
          Bienvenue dans votre bibliotèque DaVinciMedia !
        </q-toolbar-title>
      </q-toolbar>
    </q-header>
  </q-layout>
</template>

<script setup>
import { defineEmits } from 'vue';

// Déclarer l'événement "toggleLeftDrawer"
const emit = defineEmits(['toggleLeftDrawer']);

function emitToggleLeftDrawer() {
  emit('toggleLeftDrawer'); // Émettre l'événement
}

</script>

<style></style>
