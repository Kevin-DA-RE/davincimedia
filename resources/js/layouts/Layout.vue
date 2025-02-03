
<script setup>
import { ref } from "vue";


</script>


<template>
  <q-layout view="hHh Lpr lff" class="shadow-2 rounded-borders">

    <q-header>
        <q-toolbar class="bg-dark text-white shadow-2 rounded-borders">
            <!-- Titre aligné à gauche -->
            <q-toolbar-title class="q-mr-md">
                Bienvenue dans votre bibliothèque DaVinciMedia !
            </q-toolbar-title>

            <!-- Tabs centrés -->
            <q-tabs align="center" class="gt-xs">
                <q-route-tab :to="{ name: 'home' }" label="Accueil" icon="home" />
                <q-route-tab :to="{ name: 'movies' }" label="Films" class="text-teal" icon="movie" />
            </q-tabs>

            <q-space />

            <q-btn round flat icon="person">
                <q-menu>
                    <q-list style="min-width: 150px">
                        <q-item clickable v-close-popup>
                            <q-item-section>Se connecter</q-item-section>
                        </q-item>
                        <q-item clickable v-close-popup>
                            <q-item-section>S'inscrire</q-item-section>
                        </q-item>
                    </q-list>
                </q-menu>
            </q-btn>
        </q-toolbar>
    </q-header>

    <q-page-container>
          <router-view></router-view>
    </q-page-container>

  </q-layout>
</template>
