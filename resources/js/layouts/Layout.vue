<script setup>
import { onMounted, ref } from "vue";
import Auth from "./Auth.vue";
import axios from "axios";
import { useQuasar } from "quasar";

const quasar = useQuasar();
const isAuthentified = ref();

const search = ref("");
const url_backend =
    window.location.hostname == "127.0.0.1"
        ? "http://127.0.0.1:8000/"
        : import.meta.env.VITE_API_URL;

onMounted(async () => {
    isAuthentified.value = await axios
        .get(`${url_backend}api/user/check-user`, { withCredentials: true })
        .then((response) => {
            if (response.data.code === 200) {
                return true;
            } else {
                return false;
            }
        })
        .catch((error) => {
            return error.response.status;
        });
});

function authValidated(statut) {
    isAuthentified.value = statut === 200 ? true : false;
}

async function onLogout() {
    const response = await axios
        .post(`${url_backend}api/user/logout`, {}, { withCredentials: true })
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
            <Auth @authValidated="authValidated" />
        </div>
        <div v-else>
            <q-header>
                <q-toolbar class="bg-dark text-white shadow-2 rounded-borders">
                    <q-toolbar-title class="q-mr-md">
                        <!-- Affichage sur petits écrans -->
                        <div
                            v-if="quasar.screen.xs"
                            class="flex items-center justify-center"
                        >
                            <q-img
                                src="/logo_davincimedia.png"
                                alt="DavinciMedia"
                                width="100px"
                            />
                        </div>

                        <!-- Affichage sur écrans moyens et grands -->
                        <div v-else class="flex items-center">
                            <q-img
                                src="/logo_davincimedia.png"
                                alt="DavinciMedia"
                                width="100px"
                                height="100px"
                            />
                            <p class="text-h5 m-0">DaVinciMedia</p>
                        </div>
                    </q-toolbar-title>

                    <!-- Tabs centrés -->
                    <q-tabs class="flex justify-center">
                        <q-route-tab
                            :to="{ name: 'movies' }"
                            label="Films"
                            class="text-teal"
                            icon="movie"
                        />
                        <q-route-tab
                            :to="{ name: 'series' }"
                            label="Series"
                            class="text-teal"
                            icon="tv"
                        />
                    </q-tabs>

                    <q-space />
                    <q-input
                        class="q-mr-sm"
                        outlined
                        dark
                        placeholder="Rechercher..."
                        v-model="search"
                        style="width: 150px"
                    />
                    <q-btn round flat icon="person">
                        <q-menu>
                            <q-list style="min-width: 150px">
                                <q-item clickable v-close-popup>
                                    <q-item-section @click="onLogout"
                                        >Se deconnecter</q-item-section
                                    >
                                </q-item>
                            </q-list>
                        </q-menu>
                    </q-btn>
                </q-toolbar>
            </q-header>

            <q-page-container class="bg-dark">
                <router-view :search="search"></router-view>
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
