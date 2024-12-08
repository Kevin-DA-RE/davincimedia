<template>
    <q-layout view="lHh Lpr lFf" class="bg-grey-9 text-white">
      <Header @toggleLeftDrawer="toggleLeftDrawer"/>
      <q-page-container>
        <router-view :leftDrawerOpen="leftDrawerOpen"></router-view>
      </q-page-container>
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
  