<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import FormUser from "../views/slot/formUser.vue";

const formUser = ref(false);
const modeForm = ref("login");
const forgotPassword = ref(false);
const formUserName = ref("");
const formUserEmail = ref("");
const formUserPassword = ref("");
const formForgotPassword = ref("");
const confirmFormForgotPassword = ref("");
const checkAccount = ref(false);

onMounted(async () => {
    const status = await axios.get("http://127.0.0.1:8000/api/movie/show-movies")
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.status;
        });
    formUser.value = status === 401 ? true : false;
});

async function onRegister() {
    // Init FormData pour envoyer les datas
    const formData = new FormData();
    formData.append('name', formUserName.value);
    formData.append('email', formUserEmail.value);
    formData.append('password', formUserPassword.value);

    const response = await axios
        .post("http://127.0.0.1:8000/api/user/register", formData, {
            headers: {
                accept: "multipart/form-data"
            }
        })
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });

        modeForm.value = response === 200 ? 'login' : 'register';
}

async function onLogin() {
    // Init FormData pour envoyer les datas
    const formData = new FormData();
    formData.append('email', formUserEmail.value);
    formData.append('password', formUserPassword.value);

    const response = await axios
        .post("http://127.0.0.1:8000/api/user/login", formData, {
            headers: {
                accept: "multipart/form-data"
            }
        })
        .then((response) => {
            return {
                'statut': response.status,
                "data": response.data
            };
        })
        .catch((error) => {
            return error.response.data;
        });

    formUser.value = response.statut === 200 ? false : true;
}

async function onLogout() {
    const response = await axios
        .post("http://127.0.0.1:8000/api/user/logout",)
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });

    formUser.value = response === 200 ? true : false;
}


async function onResetPassword() {
    const response = await axios
        .post("http://127.0.0.1:8000/api/user/forgot-password", {
            email: formUserEmail.value,
            password: formForgotPassword.value,
        })
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });

    tab.value = response === 200 ? 'login' : tab.value;
    forgotPassword.value = false;
}

async function checkEmail() {
    const response = await axios
        .post("http://127.0.0.1:8000/api/user/check-email",)
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });

        checkAccount.value = response === 200 ? true : false;
}

async function onSubmit() {
    switch (modeForm.value) {
        case 'register':
            await onRegister();
            break;

        case 'login':
            await onLogin();
            break;

        default:
            await onResetPassword();
            break;
    }
}

function onReset() {
    formUserName.value = '';
    formUserEmail.value = '';
    formUserPassword.value = '';
    formForgotPassword.value = '';
    confirmFormForgotPassword.value = '';
}
</script>

<template>
  <q-layout view="hHh Lpr lff" class="shadow-2 rounded-borders">
    <div v-if="formUser">
        <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
            <div v-if="modeForm === 'register'">
                <q-input
                filled
                v-model="formUserName"
                label="Votre pseudo"
                />

                <q-input
                    filled
                    type="mail"
                    v-model="formUserEmail"
                    label="Votre adresse Email"
                />
                <q-input
                    filled
                    type="password"
                    v-model="formUserPassword"
                    label="Votre mot de passe"
                />
            </div>
            <div v-else-if="modeForm === 'login'">
                <q-input
                filled
                type="mail"
                v-model="formUserEmail"
                label="Votre adresse Email"
                />
                <q-input
                    filled
                    type="password"
                    v-model="formUserPassword"
                    label="Votre mot de passe"
                />
                <p style="color: blue;cursor: pointer;" @click="forgotPassword = true">Mot de passe oublié ?</p>
            </div>
            <div v-else>
                <q-input
                    filled
                    type="mail"
                    v-model="formUserEmail"
                    label="Votre adresse Email"
                    @change="checkEmail()"
                />
                <div v-if="checkAccount === true">
                    <q-input
                        filled
                        type="password"
                        v-model="formForgotPassword"
                        label="Votre nouveau mot de passe"
                    />
                    <q-input
                        filled
                        type="password"
                        v-model="confirmFormForgotPassword"
                        label="Confirmer votre mot de passe"
                    />
                </div>
                <div v-else>
                    <p style="color: red;">Veuillez saisire une autre adresse mail</p>
                </div>
            </div>
        </FormUser>
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
