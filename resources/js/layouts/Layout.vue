<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import FormUser from "../views/slot/FormUser.vue";

const formUser = ref(false);
const formDialog = ref(false);
const modeForm = ref("login");
const forgotPassword = ref(false);
const formUserName = ref("");
const formUserEmail = ref("");
const formUserPassword = ref("");
const formForgotPassword = ref("");
const confirmFormForgotPassword = ref("");
const checkAccount = ref(false);
const checkErrorMail = ref(false);
const messageError = ref()
onMounted(async () => {
    /*
    const status = await axios.get("http://127.0.0.1:8000/api/movie/show-movies")
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.status;
        });
        */
    formUser.value =  true 
    formDialog.value = true 
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
    const formData = new FormData();
    formData.append('email', formUserEmail.value);

    const response = await axios
        .post("http://127.0.0.1:8000/api/user/check-email",formData)
        .then((response) => {
            return response.data;
        })
        .catch((error) => {
            return error.response.data;
        });

        checkAccount.value = response.code === 200 ? true : false;
        checkErrorMail.value = response.code !==200 ?  true : false;

        messageError.value =  response.code !==200 ? response.message : '';
}

async function onSubmit() {
    switch (modeForm.value) {
        case 'register':
            console.log("on passe dans register ");
            await onRegister();
            break;

        case 'login':
            console.log("on passe dans login ");
            await onLogin();
            break;

        default:
            console.log("on passe dans default ");
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
function dialogFormForgotPassword() {
    modeForm.value = 'forgotPassword';
    checkAccount.value = false
    checkErrorMail.value =false
}

function redirectRegister() {
    modeForm.value = 'register';
    checkAccount.value = false
    checkErrorMail.value =false
}
</script>

<template>
  <q-layout view="hHh Lpr lff" class="shadow-2 rounded-borders">
    <div v-if="formUser">
        <q-dialog v-model="formDialog" persistent>
            <div v-if="modeForm === 'register'">

                <FormUser 
                :mode="modeForm" 
                @submit="onSubmit" 
                @reset="onReset"
                >
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
            </FormUser>

            </div>
            <div v-else-if="modeForm === 'login'">

                <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
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
                    <p class="forgotPassword"  @click="dialogFormForgotPassword()">Mot de passe oublié ?</p>
                </FormUser>

            </div>
            <div v-else>

                <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
                    <q-input
                        filled
                        type="mail"
                        v-model="formUserEmail"
                        label="Votre adresse Email"
                        @change="checkEmail()"
                    />
                    <div v-show="checkAccount">
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
                    <div v-show="checkErrorMail">
                        <p style="color: red;">{{messageError}}</p>
                        <p style="color: red;">Revenir à l'écran d'inscription ? <br> veuillez cliquez <strong style="cursor: pointer;" @click="redirectRegister()">ici </strong></p>
                    </div>
                </FormUser>

            </div>
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

<style>
.forgotPassword {
    color: blue;
    cursor: pointer;
}
</style>