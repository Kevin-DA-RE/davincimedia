<script setup>

import { onMounted, ref } from "vue";
import Auth from "./Auth.vue";
import axios from "axios";
import {useQuasar}  from "quasar"



const isAuthentified = ref();
const quasar = useQuasar()


onMounted(async () => {
    const status = await axios.get("http://127.0.0.1:8000/api/user/check-user")
        .then((response) => {
            return response.data;
        })
        .catch((error) => {
            return error.response.status;
        });
        isAuthentified.value = status.code === 200 ? true : false;
});

function authValidated() {
    isAuthentified.value = true;
}

async function onLogout() {
    const response = await axios
        .post("http://127.0.0.1:8000/api/user/logout",{}, { withCredentials: true })
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });

        isAuthentified.value = response === 200 ? false : true;
}
</script>

<template>
  <q-layout view="hHh Lpr lff" class="shadow-2 rounded-borders">
    <div v-if="!isAuthentified">
        <Auth @authValidated="authValidated"/>
    </div>
    <div v-else>
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
                                <q-item-section @click="onLogout">Se deconnecter</q-item-section>
                            </q-item>
                        </q-list>
                    </q-menu>
                </q-btn>
            </q-toolbar>
        </q-header>

        <q-page-container>
            <router-view></router-view>
        </q-page-container>
    </div>


  </q-layout>
</template>

<style>
.forgotPassword {
    color: blue;
    cursor: pointer;
}
</style>
