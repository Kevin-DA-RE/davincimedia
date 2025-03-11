<script setup>
import { ref } from 'vue'
import axios from "axios";
import FormUser from "../views/slot/FormUser.vue";

const formDialog = ref(false);
const modeForm = ref("register");
const initAuth = ref(true)
const forgotPassword = ref(false);
const formUserName = ref("");
const formUserEmail = ref("");
const formUserPassword = ref("");
const formForgotPassword = ref("");
const confirmFormForgotPassword = ref("");
const checkAccount = ref(false);
const checkErrorMail = ref(false);
const messageError = ref()

const emit = defineEmits(['authValidated'])

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
        initAuth.value = false
        formDialog.value = true
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

        formDialog.value = response.statut === 200 ? false : true;
        emit('authValidated')
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

async function checkEmailLogin() {
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

async function checkEmailRegister() {
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

        checkErrorMail.value = response.code === 400 ? false : true;
        messageError.value =  response.code === 400 ? '': response.message;
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
    onReset()
    checkAccount.value = false
    checkErrorMail.value =false
}
function Login() {
    initAuth.value = false
    formDialog.value = true
    modeForm.value = 'login';
}

function backToRegister() {
    initAuth.value = true
    formDialog.value = false
}
</script>

<template>
    <q-dialog v-model="initAuth" persistent >
        <div>
            <div>
                <p>Bienvenue sur l'écran de connection à votre application <br>
                Da Vinci Media !
                </p>
            </div>
            <div>
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
                        @change="checkEmailRegister()"
                    />
                    <q-input
                        filled
                        type="password"
                        v-model="formUserPassword"
                        label="Votre mot de passe"
                    />
                    <div v-show="checkErrorMail">
                        <p style="color: red;">{{messageError}}</p>
                    </div>
                </FormUser>
                <q-btn color="primary" label="Se connecter" @click="Login()"/>
            </div>
        </div>
    </q-dialog>
    <q-dialog v-model="formDialog" persistent>
            <div v-if="modeForm === 'login'">

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
                <q-btn color="primary" label="Revenir à la page de connexion" @click="backToRegister()"/>

            </div>
            <div v-else>

                <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
                    <q-input
                        filled
                        type="mail"
                        v-model="formUserEmail"
                        label="Votre adresse Email"
                        @change="checkEmailLogin()"
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
</template>
