<script setup>
import { defineProps, defineEmits, computed } from 'vue'

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
    <q-dialog persistent>
    <q-form
        @submit.prevent="onSubmitForm"
        @reset="onResetForm"
        class="q-gutter-md bg-white"
        >
        <h6>{{title}}</h6>
        <slot></slot>
        <div>
            <q-btn label="Annuler" type="reset" color="primary" flat class="q-ml-sm" />
            <q-btn :label="labelSubmit" type="submit" color="primary"/>
        </div>
        </q-form>
    </q-dialog>
</template>



<style scoped>
</style>
