<script setup>
import { computed } from "vue";
import { useQuasar } from "quasar";

const props = defineProps({
    mode: String,
});
const quasar = useQuasar();

const emit = defineEmits(["submit", "reset"]);

const title = computed(() => {
    switch (props.mode) {
        case "register":
            return "S'inscrire";
        case "login":
            return "Se connecter";
        case "forgotPassword":
            return "Mot de passe oublié ?";
        case "addMovies":
            return "Ajouter un ou plusieurs film(s)";
        case "addSeries":
            return "Ajouter une ou plusieurs serie(s)";
        case "updateMovie":
            return "Modifier un film";
        case "updateSerie":
            return "Modifier une serie";
        case "deleteMovie":
            return "Supprimer un film";
        case "deleteSerie":
            return "Supprimer une serie";
        default:
            return "title par default";
    }
});

const labelSubmit = computed(() => {
    switch (props.mode) {
        case "register":
            return "S'inscrire";
        case "login":
            return "Se connecter";
        case "forgotPassword":
            return "Modifier";
        case "addMovies":
            return "Ajouter";
        case "addSeries":
            return "Ajouter";
        case "updateMovie":
            return "Modifier";
        case "updateSerie":
            return "Modifier";
        case "deleteMovie":
            return "Supprimer";
        case "deleteSerie":
            return "Supprimer";
        default:
            return "submit par default";
    }
});

function onSubmitForm() {
    emit("submit");
}

function onResetForm() {
    emit("reset");
}
</script>

<template>
    <q-form
        @submit.prevent="onSubmitForm"
        @reset="onResetForm"
        class="q-my-auto bg-grey-14 rounded-lg text-white"
        style="width: 100vw; height: 100vh"
    >
        <p class="text-h6 q-pl-md q-pt-md">{{ title }}</p>
        <slot></slot>
        <div v-if="quasar.screen.xs">
            <div class="flex justify-between q-my-auto">
                <q-btn label="Annuler" type="reset" flat class="q-ma-md" />
                <q-btn
                    class="q-ma-md"
                    :label="labelSubmit"
                    type="submit"
                    color="primary"
                />
            </div>
        </div>
        <div v-else class="flex justify-start">
            <q-btn label="Annuler" type="reset" flat class="q-ma-md" />
            <q-btn
                class="q-ma-md"
                :label="labelSubmit"
                type="submit"
                color="primary"
            />
        </div>
    </q-form>
</template>

<style scoped></style>
