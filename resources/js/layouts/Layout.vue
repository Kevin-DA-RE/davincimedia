<template>
  <q-layout view="hHh Lpr lff" class="shadow-2 rounded-borders">

    <q-header>
      <q-toolbar class="text-white">
        <q-tabs align="left">
          <q-route-tab :to="{ name: 'home' }" label="Accueil" />
          <q-route-tab :to="{ name: 'movies' }" label="Films"/>
        </q-tabs>        
        <q-toolbar-title>
          Bienvenue dans votre bibliotèque DaVinciMedia !
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
          <router-view></router-view>
    </q-page-container>

  </q-layout>
</template>
  
  <script setup>
  import { ref } from "vue";
  </script>
  