<script setup>

import { onMounted, ref } from "vue";
import Auth from "./Auth.vue";
import axios from "axios";
import { useQuasar } from 'quasar';

const modeForm = ref();
const quasar = useQuasar();
const isAuthentified = ref();
const url_backend = "http://127.0.0.1:8000"

onMounted(async () => {
    const status = await axios.get(`${url_backend}/api/user/check-user`)
        .then((response) => {
            return response.data;
        })
        .catch((error) => {
            return error.response.status;
        });
        modeForm.value = status.code === 200 ? 'login' : 'register';
        isAuthentified.value = status.code === 200 ? true : false;
});

function authValidated() {
    isAuthentified.value = true;
    axios.defaults.withCredentials = true;

}

async function onLogout() {
    const response = await axios
        .post(`${url_backend}/api/user/logout`,{}, { withCredentials: true })
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });

        isAuthentified.value = response === 204 ? false : true;
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
                <q-toolbar-title class="q-mr-md">
                <!-- Affichage sur petits écrans -->
                <div v-if="quasar.screen.lt.sm" class="flex items-center justify-center">
                    <img src="../../assets/cine_hightech_3.png" alt="cine_hight_tech" style="width: 100px; height: 100px;">
                </div>

                <!-- Affichage sur écrans moyens et grands -->
                <div v-else class="flex items-center">
                    <img src="../../assets/cine_hightech_3.png" alt="cine_hight_tech" style="width: 100px; height: 100px;">
                    <p class="text-h5 m-0">DaVinciMedia</p>
                </div>
                </q-toolbar-title>

                <!-- Tabs centrés -->
                <q-tabs align="center">
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
