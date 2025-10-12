<script setup>
import { useQuasar } from "quasar";
import { ref } from "vue";

const quasar = useQuasar();

defineProps({
    media: {
        type: Object,
        required: true,
    },
});

const detailCard = ref(false);
</script>

<template>
    <!--Card pour afficher les éléments-->
    <div class="q-mt-sm q-mb-sm q-pa-sm flex justify-center">
        <q-img
            :src="media.url_img"
            style="width: 150px; height: max-content; cursor: pointer"
            @click="detailCard = true"
        />
    </div>
    <div v-if="quasar.screen.xs">
        <q-dialog v-model="detailCard">
            <q-card
                class="bg-dark text-white my-card columnq-pa-sm"
                style="height: 100%"
            >
                <q-card-section class="row items-center q-pb-none">
                    <q-space />
                    <q-btn icon="close" fix round dense v-close-popup />
                </q-card-section>

                <q-card-section
                    class="flex justify-between"
                    style="max-width: 100%"
                >
                    <div v-for="genres in media.genres" :key="genres.id">
                        <q-badge
                            outline
                            color="primary"
                            :label="genres.name"
                            class="text-h5"
                        />
                    </div>
                </q-card-section>
                <q-card-section>
                    <q-img
                        :src="media.url_img"
                        style="
                            max-width: 100%;
                            height: auto;
                            background-size: contain;
                        "
                    />
                </q-card-section>
                <!-- Section scrollable contenant le synopsis -->
                <q-card-section class="q-px-sm q-pt-none scrollable-synopsis">
                    <h4>Résumé</h4>
                    <div class="text-h6">
                        {{ media.synopsis }}
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>
    </div>

    <div v-if="!quasar.screen.xs">
        <q-dialog v-model="detailCard" full-width full-height>
            <q-card
                class="bg-dark text-white my-card columnq-pa-sm"
                style="height: 100%"
            >
                <q-card-section class="row items-center q-pb-none">
                    <q-space />
                    <q-btn icon="close" fix round dense v-close-popup />
                </q-card-section>

                <div class="row">
                    <div class="col">
                        <q-card-section class="flex justify-around">
                            <div
                                v-for="genres in media.genres"
                                :key="genres.id"
                            >
                                <q-badge
                                    outline
                                    color="primary"
                                    :label="genres.name"
                                    class="text-h5"
                                />
                            </div>
                        </q-card-section>
                        <q-card-section>
                            <q-img
                                :src="media.url_img"
                                style="
                                    max-width: 100%;
                                    height: 500px;
                                    background-size: contain;
                                "
                            />
                        </q-card-section>
                    </div>
                    <div class="col">
                        <q-card-section
                            class="q-px-sm q-pt-none scrollable-synopsis"
                        >
                            <h4>Résumé</h4>
                            <div class="text-h6">
                                {{ media.synopsis }}
                            </div>
                        </q-card-section>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    </div>
</template>

<style>
.my-card {
    width: 100%;
    max-width: 250px;
}

.scrollable-synopsis {
    flex: 1;
    overflow-y: auto;
}
</style>
