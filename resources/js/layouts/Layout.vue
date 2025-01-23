<template>
  <q-layout view="hHh Lpr lff" class="shadow-2 rounded-borders">

    <q-header>
        <q-toolbar class="bg-dark text-white shadow-2 rounded-borders">
            <q-tabs align="left">
            <q-toolbar-title>
                Bienvenue dans votre bibliotèque DaVinciMedia !
            </q-toolbar-title>

                <q-route-tab :to="{ name: 'home' }" label="Accueil"  icon="home"/>
                <q-route-tab :to="{ name: 'movies' }" label="Films"class="text-teal" icon="movie" />
            </q-tabs>
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
