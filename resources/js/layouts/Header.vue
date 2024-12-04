
<script setup>

</script>

<template>
  <q-layout view="lHh Lpr lFf" class="bg-grey-9 text-white">
    <q-header elevated class="bg-grey-6">
      <q-toolbar>
        <router-link :to="{ name: 'home' }"><q-btn label="Accueil" hidden id="btnHome"/></router-link>
        <router-link :to="{ name: 'movies' }" hidden><q-btn label="Films" hidden id="btnMovie"/></router-link>
        <q-toolbar-title>
          Bienvenue dans votre bibliotèque DaVinciMedia !
        </q-toolbar-title>
      </q-toolbar>
    </q-header>    
    <div class="container">
        <router-view></router-view>
    </div>
  </q-layout>
</template>

<style></style>
