<script setup>
import {  computed } from 'vue'
import { useQuasar } from 'quasar';



const props = defineProps({
    mode: String
})

const quasar = useQuasar();
const emit = defineEmits(['submit', 'reset'])

const title = computed(() => {
    switch (props.mode) {
        case 'register':
            return "S'inscrire"
        case 'login':
            return "Se connecter"
        default:
            return "Mot de passe oublié ?"
    }
})

const labelSubmit = computed(() => {
    switch (props.mode) {
        case 'register':
            return "S'inscrire"
        case 'login':
            return "Se connecter"
        default:
            return "Réinitialiser mot de passe"
    }
})

function onSubmitForm(){
    emit('submit')
}

function onResetForm(){
    emit('reset')
}
</script>



<template>
    <p class="text-h6 text-center">Bienvenue sur Da Vinci Media !</p>
    <q-form
        @submit.prevent="onSubmitForm"
        @reset="onResetForm"
        class="q-gutter-md bg-white"
        dark
        >
        <p class="text-h6 q-pl-md q-pt-md">{{title}}</p>
        <slot></slot>
        <div class="flex justify-around">
            <q-btn label="Annuler" type="reset" flat class="q-ma-md" />
            <q-btn class="q-ma-md"  :label="labelSubmit" type="submit" color="primary"/>
        </div>
        </q-form>
</template>



<style scoped>
</style>
