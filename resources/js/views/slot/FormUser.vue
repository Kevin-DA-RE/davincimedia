<script setup>
import {  computed } from 'vue'

const props = defineProps({
    mode: String
})

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
<div class="q-mb-md">
    <p class="text-h6">Bienvenue sur Da Vinci Media !</p>
    <q-form
        @submit.prevent="onSubmitForm"
        @reset="onResetForm"
        class="q-gutter-md bg-white"
        dark
        >
        <p class="text-h6 q-pl-md q-pt-md">{{title}}</p>
        <slot></slot>
        <div >
            <q-btn label="Annuler" type="reset" color="primary" flat class="q-ma-md" />
            <q-btn :label="labelSubmit" type="submit" color="primary"/>
        </div>
        </q-form>
    </div>
</template>



<style scoped>
</style>
