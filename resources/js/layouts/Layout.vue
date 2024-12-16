<template>
  <q-layout view="hHh Lpr lff" class="bg-grey-9 shadow-2 rounded-borders">

    <q-header class="bg-grey-6">
      <q-toolbar class="text-white">
        <q-tabs align="left">
          <q-route-tab :to="{ name: 'home' }" label="Accueil" />
          <q-route-tab :to="{ name: 'movies' }" label="Films" @click="toggleLeftDrawer"/>
        </q-tabs>        
        <q-toolbar-title>
          Bienvenue dans votre bibliotèque DaVinciMedia !
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
              <router-view :leftDrawerOpen="leftDrawerOpen"></router-view>
    </q-page-container>

  </q-layout>
</template>
  
  <script setup>
  import { ref } from "vue";
  
  const leftDrawerOpen = ref(false);

  function toggleLeftDrawer () {
    leftDrawerOpen.value = !leftDrawerOpen.value
  }


  </script>
  