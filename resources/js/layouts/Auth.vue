<script setup>
import { ref } from "vue";
import axios from "axios";
import FormUser from "../views/slot/Form.vue";
import { useQuasar } from "quasar";

const initAuthDialog = ref(true);
const showFormLogin = ref(true);
const showFormRegister = ref(false);
const showFormForgotPassword = ref(false);
const modeForm = ref("login");
const formUserName = ref("");
const formUserEmail = ref("");
const formUserPassword = ref("");
const formForgotPassword = ref("");
const confirmFormForgotPassword = ref("");
const checkAccount = ref(false);
const checkErrorMail = ref(false);
const messageError = ref();
const quasar = useQuasar();

const emit = defineEmits(["authValidated", "redirectRegister"]);
const url_backend =
    window.location.hostname == "127.0.0.1"
        ? "http://127.0.0.1:8000/"
        : import.meta.env.VITE_API_URL;

async function onRegister() {
    // Init FormData pour envoyer les datas
    const formData = new FormData();
    formData.append("name", formUserName.value);
    formData.append("email", formUserEmail.value);
    formData.append("password", formUserPassword.value);

    const response = await axios
        .post(`${url_backend}api/user/register`, formData, {
            headers: {
                accept: "multipart/form-data",
            },
        })
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });

    modeForm.value = response === 204 ? "login" : "register";
}

async function onLogin() {
    // Init FormData pour envoyer les datas
    const formData = new FormData();
    formData.append("email", formUserEmail.value);
    formData.append("password", formUserPassword.value);

    await axios
        .post(`${url_backend}api/user/login`, formData, {
            headers: {
                accept: "multipart/form-data",
            },
            withCredentials: true,
        })
        .then((response) => {
            return {
                statut: response.status,
                data: response.data,
            };
        })
        .catch((error) => {
            return error.response.data;
        });

    emit("authValidated");
}

async function onResetPassword() {
    await axios
        .post(`${url_backend}api/user/forgot-password`, {
            email: formUserEmail.value,
            password: formForgotPassword.value,
        })
        .then((response) => {
            return response.status;
        })
        .catch((error) => {
            return error.response.data;
        });
    modeForm.value = "login";
}

async function checkEmailLogin() {
    const formData = new FormData();
    formData.append("email", formUserEmail.value);

    const response = await axios
        .post(`${url_backend}api/user/check-email`, formData)
        .then((response) => {
            return response.data;
        })
        .catch((error) => {
            return error.response.data;
        });

    checkAccount.value = response.code === 200 ? true : false;
    checkErrorMail.value = response.code !== 200 ? true : false;

    messageError.value = response.code !== 200 ? response.message : "";
}

async function checkEmailRegister() {
    const formData = new FormData();
    formData.append("email", formUserEmail.value);

    const response = await axios
        .post(`${url_backend}api/user/check-email`, formData)
        .then((response) => {
            return response.data;
        })
        .catch((error) => {
            return error.response.data;
        });

    checkErrorMail.value = response.code === 400 ? true : false;
    messageError.value = response.code === 400 ? "" : response.message;
}

async function onSubmit() {
    switch (modeForm.value) {
        case "register":
            await onRegister();
            break;

        case "login":
            await onLogin();
            break;

        default:
            await onResetPassword();
            break;
    }
}

function onReset() {
    formUserName.value = "";
    formUserEmail.value = "";
    formUserPassword.value = "";
    formForgotPassword.value = "";
    confirmFormForgotPassword.value = "";

    switch (modeForm.value) {
        case "forgotPassword":
            showFormLogin.value = true;
            showFormForgotPassword.value = false;
            showFormRegister.value = false;
            modeForm.value = "login";
            break;

        case "register":
            showFormLogin.value = false;
            showFormForgotPassword.value = false;
            showFormRegister.value = true;
            break;
    }
}
function dialogFormForgotPassword() {
    modeForm.value = "forgotPassword";
    checkAccount.value = false;
    checkErrorMail.value = false;
    showFormLogin.value = false;
    showFormRegister.value = false;
    showFormForgotPassword.value = true;
}

function redirectRegister() {
    modeForm.value = "register";
    onReset();
    checkAccount.value = false;
    checkErrorMail.value = false;
    showFormLogin.value = false;
    showFormRegister.value = true;
}

function redirectLogin() {
    modeForm.value = "login";
    checkAccount.value = false;
    checkErrorMail.value = false;
    showFormLogin.value = true;
    showFormRegister.value = false;
}
</script>

<template>
    <div
        v-if="quasar.screen.xs"
        style="width: 100vw; height: 100vh"
        class="bg-grey-14"
    >
        <div class="row justify-center">
            <q-img
                src="/logo_davincimedia.png"
                alt="DavinciMedia"
                width="100px"
            />

            <h6 class="text-white">DavinciMedia</h6>
        </div>
        <!--LOGIN-->
        <div v-show="showFormLogin">
            <FormUser
                :mode="modeForm"
                @submit="onSubmit"
                @reset="onReset"
                @redirectRegister="redirectRegister"
                class="q-my-auto rounded-lg text-white"
            >
                <q-input
                    outlined
                    type="mail"
                    v-model="formUserEmail"
                    class="q-pa-md rounded-lg"
                    label="Votre adresse Email"
                    bg-color="blue-grey-2"
                    @change="checkEmailLogin()"
                />
                <q-input
                    outlined
                    type="password"
                    v-model="formUserPassword"
                    class="q-pa-md text-white"
                    label="Votre mot de passe"
                    bg-color="blue-grey-2"
                />
                <p
                    class="forgotPassword q-pl-md"
                    @click="dialogFormForgotPassword()"
                >
                    Mot de passe oublié ?
                </p>
                <div v-show="checkErrorMail" class="q-pl-md">
                    <p style="color: orange">{{ messageError }}</p>
                </div>
                <q-btn
                    label="S'inscrire"
                    @click="redirectRegister()"
                    class="q-ma-md"
                    color="secondary"
                />
            </FormUser>
        </div>

        <!--REGISTER-->
        <div v-show="showFormRegister">
            <FormUser
                :mode="modeForm"
                @submit="onSubmit"
                @reset="onReset"
                class="q-my-auto bg-grey-14 rounded-lg text-white"
            >
                <q-input
                    outlined
                    v-model="formUserName"
                    label="Votre pseudo"
                    class="q-pa-md text-white"
                    bg-color="blue-grey-2"
                />

                <q-input
                    outlined
                    type="mail"
                    v-model="formUserEmail"
                    label="Votre adresse Email"
                    class="q-pa-md text-white"
                    bg-color="blue-grey-2"
                    @change="checkEmailRegister()"
                />
                <q-input
                    outlined
                    type="password"
                    v-model="formUserPassword"
                    class="q-pa-md"
                    label="Votre mot de passe"
                    bg-color="blue-grey-2"
                />
                <div v-show="checkErrorMail">
                    <p style="color: red">{{ messageError }}</p>
                </div>
                <p style="color: blue" class="q-pl-sm">
                    Vous souhaitez vous connecter ? <br />
                    veuillez cliquez
                    <strong style="cursor: pointer" @click="redirectLogin()"
                        >ici
                    </strong>
                </p>
            </FormUser>
        </div>
        <div v-show="showFormForgotPassword">
            <FormUser
                v-if="modeForm === 'forgotPassword'"
                :mode="modeForm"
                @submit="onSubmit"
                @reset="onReset"
                class="q-my-auto bg-grey-14 rounded-lg text-white"
            >
                <q-input
                    outlined
                    type="mail"
                    v-model="formUserEmail"
                    class="q-pa-md text-white"
                    bg-color="blue-grey-2"
                    label="Votre adresse Email"
                    @change="checkEmailLogin()"
                />
                <div v-show="checkAccount">
                    <q-input
                        outlined
                        type="password"
                        v-model="formForgotPassword"
                        class="q-pa-md text-white"
                        bg-color="blue-grey-2"
                        label="Votre nouveau mot de passe"
                    />
                    <q-input
                        outlined
                        type="password"
                        v-model="confirmFormForgotPassword"
                        class="q-pa-md text-white"
                        bg-color="blue-grey-2"
                        label="Confirmer votre mot de passe"
                    />
                </div>
                <div v-show="checkErrorMail" class="q-pl-md">
                    <p style="color: red">{{ messageError }}</p>
                    <p style="color: red" class="q-pl-sm">
                        Revenir à l'écran d'inscription ? <br />
                        veuillez cliquez
                        <strong
                            style="cursor: pointer"
                            @click="redirectRegister()"
                            >ici
                        </strong>
                    </p>
                </div>
            </FormUser>
        </div>
    </div>
    <div
        v-else
        class="bg-dark row items-center justify-around"
        :v-model="initAuthDialog"
    >
        <q-img src="/logo_davincimedia.png" height="100vh" width="800px">
            <div
                class="absolute-top bg-transparent text-center text-h2 text-cyan-12"
            >
                DavinciMedia
            </div>
        </q-img>
        <!--LOGIN-->
        <div v-show="showFormLogin">
            <FormUser
                :mode="modeForm"
                @submit="onSubmit"
                @reset="onReset"
                @redirectRegister="redirectRegister"
                class="q-my-auto bg-grey-14 rounded-lg text-white"
                style="width: 320px; height: max-content; border-radius: 20px"
            >
                <q-input
                    outlined
                    type="mail"
                    v-model="formUserEmail"
                    class="q-pa-md text-white"
                    bg-color="blue-grey-2"
                    label="Votre adresse Email"
                    @change="checkEmailLogin()"
                />
                <q-input
                    outlined
                    type="password"
                    v-model="formUserPassword"
                    class="q-pa-md text-white"
                    bg-color="blue-grey-2"
                    label="Votre mot de passe"
                />
                <p
                    class="forgotPassword q-pl-md"
                    @click="dialogFormForgotPassword()"
                >
                    Mot de passe oublié ?
                </p>
                <div v-show="checkErrorMail" class="q-pl-md">
                    <p style="color: orange">{{ messageError }}</p>
                </div>
                <q-btn
                    label="S'inscrire"
                    @click="redirectRegister()"
                    class="q-ma-md"
                    color="secondary"
                />
            </FormUser>
        </div>

        <!--REGISTER-->
        <div v-show="showFormRegister">
            <FormUser
                :mode="modeForm"
                @submit="onSubmit"
                @reset="onReset"
                class="q-my-auto bg-grey-14 rounded-lg text-white"
                style="width: 320px; height: 450px; border-radius: 20px"
            >
                <q-input
                    outlined
                    v-model="formUserName"
                    label="Votre pseudo"
                    class="q-pa-md text-white"
                    bg-color="blue-grey-2"
                />

                <q-input
                    outlined
                    type="mail"
                    v-model="formUserEmail"
                    label="Votre adresse Email"
                    class="q-pa-md text-white"
                    bg-color="blue-grey-2"
                    @change="checkEmailRegister()"
                />
                <q-input
                    outlined
                    type="password"
                    v-model="formUserPassword"
                    class="q-pa-md"
                    label="Votre mot de passe"
                    bg-color="blue-grey-2"
                />
                <div v-show="checkErrorMail">
                    <p style="color: red">{{ messageError }}</p>
                </div>
                <p style="color: blue" class="q-pl-sm">
                    Vous souhaitez vous connecter ? <br />
                    veuillez cliquez
                    <strong style="cursor: pointer" @click="redirectLogin()"
                        >ici
                    </strong>
                </p>
            </FormUser>
        </div>
        <div v-show="showFormForgotPassword">
            <FormUser
                v-if="modeForm === 'forgotPassword'"
                :mode="modeForm"
                @submit="onSubmit"
                @reset="onReset"
                class="q-my-auto bg-grey-14 rounded-lg text-white"
                style="width: 320px; height: max-content; border-radius: 20px"
            >
                <q-input
                    outlined
                    type="mail"
                    v-model="formUserEmail"
                    class="q-pa-md text-white"
                    bg-color="blue-grey-2"
                    label="Votre adresse Email"
                    @change="checkEmailLogin()"
                />
                <div v-show="checkAccount">
                    <q-input
                        outlined
                        type="password"
                        v-model="formForgotPassword"
                        class="q-pa-md text-white"
                        bg-color="blue-grey-2"
                        label="Votre nouveau mot de passe"
                    />
                    <q-input
                        outlined
                        type="password"
                        v-model="confirmFormForgotPassword"
                        class="q-pa-md text-white"
                        bg-color="blue-grey-2"
                        label="Confirmer votre mot de passe"
                    />
                </div>
                <div v-show="checkErrorMail" class="q-pl-md">
                    <p style="color: red">{{ messageError }}</p>
                    <p style="color: red" class="q-pl-sm">
                        Revenir à l'écran d'inscription ? <br />
                        veuillez cliquez
                        <strong
                            style="cursor: pointer"
                            @click="redirectRegister()"
                            >ici
                        </strong>
                    </p>
                </div>
            </FormUser>
        </div>
    </div>
</template>
