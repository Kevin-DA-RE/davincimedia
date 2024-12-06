<template>
    <q-layout view="lHh Lpr lFf" class="bg-grey-9 text-white">
      <Header @toggleLeftDrawer="toggleLeftDrawer"/>
      <div class="container">
        <router-view :leftDrawerOpen="leftDrawerOpen"></router-view>
    </div>
    </q-layout>
  </template>
  
  <script setup>
  import Header from '../views/Header.vue';
  import { ref } from "vue";
  
  const leftDrawerOpen = ref(false);

  function toggleLeftDrawer () {
    leftDrawerOpen.value = !leftDrawerOpen.value
  }


  </script>
  