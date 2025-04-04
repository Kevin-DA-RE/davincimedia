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
        case 'addMovies':
            return "Ajouter un ou plusieurs film(s)"
        case 'updateMovie':
            return "Modifier un film"
        case 'deleteMovie':
            return "Supprimer un film"
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
        case 'addMovies':
            return "Ajouter"
        case 'updateMovie':
            return "Modifier"
        case 'deleteMovie':
            return "Supprimer"
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
    <q-form
        @submit.prevent="onSubmitForm"
        @reset="onResetForm"
        class=" bg-white"
        >
        <p class="text-h6 q-pl-md q-pt-md">{{title}}</p>
        <slot></slot>
        <div class="flex justify-start">
            <div v-if="props.mode !=='register'">
            <q-btn label="Annuler" type="reset" flat class="q-ma-md" />
            </div>
            <q-btn class="q-ma-md"  :label="labelSubmit" type="submit" color="primary"/>
        </div>
        </q-form>
</template>



<style scoped>
</style>
