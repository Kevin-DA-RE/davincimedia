<script setup>
import { ref, computed } from 'vue'
import axios from "axios";
import FormUser from "../views/slot/Form.vue";
import { useQuasar } from 'quasar';


const initAuth = ref(false);
const initAuthDialog = ref(true)
const initAuthMobile = ref(false)
const modeForm = ref();
const formUserName = ref("");
const formUserEmail = ref("");
const formUserPassword = ref("");
const formForgotPassword = ref("");
const confirmFormForgotPassword = ref("");
const checkAccount = ref(false);
const checkErrorMail = ref(false);
const messageError = ref()
const quasar = useQuasar();

const emit = defineEmits(['authValidated'])
const url_backend = window.location.hostname == "127.0.0.1" ? "http://127.0.0.1:8000" : import.meta.env.VITE_API_URL

async function onRegister() {
    // Init FormData pour envoyer les datas
    const formData = new FormData();
    formData.append('name', formUserName.value);
    formData.append('email', formUserEmail.value);
    formData.append('password', formUserPassword.value);

    const response = await axios
        .post(`${url_backend}/api/user/register`, formData, {
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

    modeForm.value = response === 204 ? 'login':'register';
}

async function onLogin() {
    // Init FormData pour envoyer les datas
    const formData = new FormData();
    formData.append('email', formUserEmail.value);
    formData.append('password', formUserPassword.value);

    await axios
        .post(`${url_backend}/api/user/login`, formData, {
            headers: {
                accept: "multipart/form-data"
            }
            ,withCredentials: true
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

    emit('authValidated')
}

async function onResetPassword() {
    await axios
        .post(`${url_backend}/api/user/forgot-password`, {
            email: formUserEmail.value,
            password: formForgotPassword.value,
        })
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });
    modeForm.value = "login"
}

async function checkEmailLogin() {
    const formData = new FormData();
    formData.append('email', formUserEmail.value);

    const response = await axios
        .post(`${url_backend}/api/user/check-email`, formData)
        .then((response) => {
            return response.data;
        })
        .catch((error) => {
            return error.response.data;
        });

    checkAccount.value = response.code === 200 ? true : false;
    checkErrorMail.value = response.code !== 200 ? true : false;

    messageError.value = response.code !== 200 ? response.message : '';
}

async function checkEmailRegister() {
    const formData = new FormData();
    formData.append('email', formUserEmail.value);

    const response = await axios
        .post(`${url_backend}/api/user/check-email`, formData)
        .then((response) => {
            return response.data;
        })
        .catch((error) => {
            return error.response.data;
        });

    checkErrorMail.value = response.code === 400 ? false : true;
    messageError.value = response.code === 400 ? '' : response.message;
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

    switch (modeForm.value) {
        case "login":
            modeForm.value = 'register';
            break;
        case "forgotPassword":
            modeForm.value = 'login';
            break;

        default:
            modeForm.value = 'register';
            break;
    }

}
function dialogFormForgotPassword() {
    modeForm.value = 'forgotPassword';
    checkAccount.value = false
    checkErrorMail.value = false
}

function redirectRegister() {
    modeForm.value = 'register';
    onReset()
    checkAccount.value = false
    checkErrorMail.value = false
}

function redirectLogin() {
    modeForm.value = 'login';
    checkAccount.value = false
    checkErrorMail.value = false
}

function Register() {
    modeForm.value = 'register';
    initAuthDialog.value = false
    initAuth.value = true
    initAuthMobile.value = true
}
function Login() {
    modeForm.value = 'login';
    initAuthDialog.value =false
    initAuth.value = true
    initAuthMobile.value = true
}

</script>

<template>

    <q-dialog v-model="initAuthDialog" persistent>
        <q-card>
            <q-card-section>
                    <div class="text-h4 text-center">Bienvenue <br>dans votre application <br>Da Vinci Media</div>
            </q-card-section>
            <q-card-actions class="flex-row justify-around">
                <q-btn color="primary" label="S'inscrire'" @click="Register()" />
                <q-btn color="primary" label="Se connecter" @click="Login()" />
            </q-card-actions>
        </q-card>
    </q-dialog>
    <div v-if="quasar.screen.lt.md" v-show="initAuthMobile">
        <div v-if="modeForm === 'register'" class="q-mb-md">
            <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
                <q-input filled v-model="formUserName" label="Votre pseudo" class="q-pa-md" />

                <q-input filled type="mail" v-model="formUserEmail" label="Votre adresse Email" class="q-pa-md"
                    @change="checkEmailRegister()" />
                <q-input filled type="password" v-model="formUserPassword" class="q-pa-md" label="Votre mot de passe" />
                <div v-show="checkErrorMail">
                    <p style="color: red;">{{ messageError }}</p>
                </div>
                <p style="color: blue;" class="q-pl-sm">Vous souhaitez vous connecter ? <br> veuillez cliquez <strong
                    style="cursor: pointer;" @click="redirectLogin()">ici </strong></p>
            </FormUser>

        </div>
        <div v-else-if="modeForm === 'login'">
            <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
                <q-input filled type="mail" v-model="formUserEmail" class="q-pa-md" label="Votre adresse Email" />
                <q-input filled type="password" v-model="formUserPassword" class="q-pa-md" label="Votre mot de passe" />
                <p class="forgotPassword q-pl-md" @click="dialogFormForgotPassword()">Mot de passe oublié ?</p>
            </FormUser>
        </div>
        <div v-else>
            <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
                <q-input filled type="mail" v-model="formUserEmail" class="q-pa-md" label="Votre adresse Email"
                    @change="checkEmailLogin()" />
                <div v-show="checkAccount">
                    <q-input filled type="password" v-model="formForgotPassword" class="q-pa-md"
                        label="Votre nouveau mot de passe" />
                    <q-input filled type="password" v-model="confirmFormForgotPassword" class="q-pa-md"
                        label="Confirmer votre mot de passe" />
                </div>
                <div v-show="checkErrorMail" class="q-pl-md">
                    <p style="color: red;">{{ messageError }}</p>
                    <p style="color: red;" class="q-pl-sm">Revenir à l'écran d'inscription ? <br> veuillez cliquez <strong
                            style="cursor: pointer;" @click="redirectRegister()">ici </strong></p>
                </div>
            </FormUser>
        </div>
    </div>
    <div v-else>
        <q-dialog v-model="initAuth" persistent>
            <div class="q-mb-md">
                <div v-if="modeForm === 'register'">
                    <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
                        <q-input filled v-model="formUserName" label="Votre pseudo" class="q-pa-md" />

                        <q-input filled type="mail" v-model="formUserEmail" label="Votre adresse Email" class="q-pa-md"
                            @change="checkEmailRegister()" />
                        <q-input filled type="password" v-model="formUserPassword" class="q-pa-md" label="Votre mot de passe" />
                        <div v-show="checkErrorMail">
                            <p style="color: red;">{{ messageError }}</p>
                        </div>
                        <p style="color: blue;" class="q-pl-sm">Vous souhaitez vous connecter ? <br> veuillez cliquez <strong
                            style="cursor: pointer;" @click="redirectLogin()">ici </strong></p>
                    </FormUser>
                </div>
                <div v-else-if="modeForm === 'login'">
                    <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
                        <q-input filled type="mail" v-model="formUserEmail" class="q-pa-md"
                            label="Votre adresse Email" />
                        <q-input filled type="password" v-model="formUserPassword" class="q-pa-md"
                            label="Votre mot de passe" />
                        <p class="forgotPassword q-pl-md" @click="dialogFormForgotPassword()">Mot de passe oublié ?</p>
                    </FormUser>
                </div>
                <div v-else-if="modeForm === 'forgotPassword'">
                    <FormUser :mode="modeForm" @submit="onSubmit" @reset="onReset">
                        <q-input filled type="mail" v-model="formUserEmail" class="q-pa-md"
                            label="Votre adresse Email" @change="checkEmailLogin()" />
                        <div v-show="checkAccount">
                            <q-input filled type="password" v-model="formForgotPassword" class="q-pa-md"
                                label="Votre nouveau mot de passe" />
                            <q-input filled type="password" v-model="confirmFormForgotPassword" class="q-pa-md"
                                label="Confirmer votre mot de passe" />
                        </div>
                        <div v-show="checkErrorMail">
                            <p style="color: red;">{{ messageError }}</p>
                            <p style="color: red;">Revenir à l'écran d'inscription ? <br> veuillez cliquez <strong
                                    style="cursor: pointer;" @click="redirectRegister()">ici </strong></p>
                        </div>
                    </FormUser>
                </div>
            </div>
        </q-dialog>
    </div>
</template>
