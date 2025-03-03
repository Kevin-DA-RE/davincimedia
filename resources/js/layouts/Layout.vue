<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const formUser = ref(false);
const forgotPassword = ref(false);
const formUserName = ref();
const formUserEmail = ref();
const formUserPassword = ref();
const tab = ref('register');


onMounted(async () => {

const status = await axios.get("http://127.0.0.1:8000/api/movie/show-movies")
    .then((response) => {
        return response.status;
    })
    .catch((error) => {
        return error.response.status;
    });
formUser.value = status === 401 ? true : false;
tab.value = 'login';
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

        tab.value = response === 200 ? 'login': 'register';

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
        .post("http://127.0.0.1:8000/api/user/forgot-password",)
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });

        tab.value = response === 200 ? 'login': tab.value;
        forgotPassword.value = false;

}

async function onCancelPassword() {
    forgotPassword.value = false;
}


</script>

<template>

  <q-layout view="hHh Lpr lff" class="shadow-2 rounded-borders">
    <div v-if="formUser">
        <q-dialog v-model="formUser" persistent>
            <q-card>
                <q-tabs
                    v-model="tab"
                    dense
                    class="text-grey"
                    active-color="primary"
                    indicator-color="primary"
                    align="justify"
                    narrow-indicator
                    >
                    <q-tab name="register" label="S'inscrire" />
                    <q-tab name="login" label="Se connecter" />
                </q-tabs>

                <q-separator />

                <q-tab-panels v-model="tab" animated>
                    <q-tab-panel name="register">
                        <q-form
                        @submit="onRegister"
                        @reset="onReset"
                        class="q-gutter-md bg-white"
                        >
                        <h6>S'inscrire</h6>
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
                            <div>
                                <q-btn label="Annuler" type="reset" color="primary" flat class="q-ml-sm" />
                                <q-btn label="S'inscrire" type="submit" color="primary"/>
                            </div>
                        </q-form>
                    </q-tab-panel>

                    <q-tab-panel name="login">
                        <q-form
                        @submit="onLogin"
                        @reset="onReset"
                        class="q-gutter-md bg-white"
                        >
                        <h6>Se connecter</h6>
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
                            <div>
                                <q-btn label="Annuler" type="reset" color="primary" flat class="q-ml-sm" />
                                <q-btn label="Se connecter" type="submit" color="primary"/>
                            </div>
                        </q-form>
                    </q-tab-panel>

                </q-tab-panels>
                <q-dialog name="forgotPassword" v-model="forgotPassword" persistent>
                        <q-form
                        @submit="onResetPassword"
                        @reset="onCancelPassword"
                        class="q-gutter-md bg-white"
                        >
                        <h6>Mot de passe oublié</h6>
                            <q-input
                                filled
                                type="password"
                                v-model="formForgotPassword"
                                label="Veuillez saisir votre nouveau mot de passe"
                            />

                            <q-input
                                filled
                                type="password"
                                v-model="confirmFormForgotPassword"
                                label="Confirmer votre nouveau mot de passe"
                            />
                            <div>
                                <q-btn label="Annuler" type="reset" color="primary" flat class="q-ml-sm" />
                                <q-btn label="Réinitialiser votre mot de passe" type="submit" color="primary"/>
                            </div>
                        </q-form>
                    </q-dialog>
            </q-card>
        </q-dialog>
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
