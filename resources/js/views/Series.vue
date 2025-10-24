<script setup>
import axios from "axios";
import { onMounted, ref, computed } from "vue";
import Media from "./component/Media.vue";
import Form from "../views/slot/Form.vue";
import { useQuasar } from "quasar";

const props = defineProps({
    search: {
        type: String,
    },
});
axios.defaults.withCredentials = true;

const checkSeries = ref();
const quasar = useQuasar();
const carouselSlide = ref(0);
const seriesList = ref([]);
const seriesListLoaded = ref([]);
const genresListLoaded = ref([]);
const formAddSeries = ref(false);
const formUpdateSerie = ref(false);
const formDeleteSerie = ref(false);
const editMode = ref(false);
const serieName = ref("");
const serie = ref({});
const serieSearched = ref({});
const serieSelected = ref({});
const serieIdOrigin = ref();
const serieIndex = ref();
const tab = ref("all");
const splitterModel = ref(20);
const panelGenre = ref("");
const slide = ref(0);
const createserie = ref(false);

const url_backend =
    window.location.hostname == "127.0.0.1"
        ? "http://127.0.0.1:8000/"
        : import.meta.env.VITE_API_URL;
const url_base =
    window.location.hostname == "127.0.0.1"
        ? "http://127.0.0.1:8000/api/media/serie"
        : import.meta.env.VITE_API_URL_SERIE;
const api = {
    url_backend_create_series: `${url_base}/create-series`,
    url_backend_update_serie: `${url_base}/update-serie`,
    url_backend_delete_serie: `${url_base}/delete-serie`,
    url_backend_show_series_by_user: `${url_base}/show-series-by-user`,
    url_backend_show_genres_series: `${url_base}/show-genres-series`,
    url_backend_get_serie_genres: `${url_base}/get-serie-with-genres`,
};

onMounted(async () => {
    quasar.loading.show({
        message: "Chargement des series en cours ...",
    });
    try {
        checkSeries.value = await axios
            .get(`${url_backend}api/user/check-series`)
            .then((response) => {
                if (response.data.code === 200) {
                    return true;
                } else {
                    return false;
                }
            })
            .catch((error) => {
                return error.response;
            });
        await loadSeriesWithGenres();
    } catch (error) {
        console.log("error onMounted" + error);
    } finally {
        quasar.loading.hide();
    }
});

async function loadSeriesWithGenres() {
    await showSeriesByUser();
    await showGenres();
}

async function showSeriesByUser() {
    try {
        const series = await axios.get(api.url_backend_show_series_by_user, {
            headers: {
                accept: "application/json",
            },
        });

        seriesListLoaded.value = series.data;
    } catch (error) {
        return error.response.data;
    }
}

async function showGenres() {
    try {
        const genres = await axios.get(api.url_backend_show_genres_series, {
            headers: {
                accept: "application/json",
            },
        });
        genres.data.unshift({
            id: 0,
            name: "all",
        });
        genresListLoaded.value = genres.data;
    } catch (error) {
        return error.response.data;
    }
}

async function showSeriesWithGenres(genre) {
    if (genre.id === 0) {
        await showSeriesByUser();
    } else {
        const url = `${api.url_backend_show_genres_series}/${genre.id}`;
        panelGenre.value = genre.name;
        const seriesWithGenres = await axios
            .get(
                url,
                { id: genre.id },
                {
                    headers: {
                        accept: "application/json",
                    },
                }
            )
            .then((serie) => serie.data)
            .catch((error) => {
                return error.response.data;
            });
        seriesListLoaded.value = seriesWithGenres;
    }
}

// Recherche du film et de son/ses genre(s)
async function getSerieWithGenre(name) {
    const url = `${api.url_backend_get_serie_genres}/${name}`;
    if (formAddSeries.value) {
        return (serieSearched.value = await axios
            .get(url)
            .then((serie) => serie.data)

            .catch((error) => {
                return error.response.data;
            }));
    } else {
        return (serieSelected.value = await axios
            .get(url)
            .then((serie) => serie.data)
            .catch((error) => {
                return error.response.data;
            }));
    }
}

function AddSerie(serie) {
    if (serie.id_serie) {
        seriesList.value.push(serie);
        serieSearched.value = {};
        serieName.value = "";
        createserie.value = true;
    }
}

async function onSubmit(form) {
    switch (form) {
        case "addSeries":
            quasar.loading.show({
                message: "Enregistrement des series en cours ...",
            });
            await createSeriesToBackEnd(seriesList.value);
            seriesList.value.length = 0;
            await loadSeriesWithGenres();
            checkSeries.value = true;
            formAddSeries.value = false;
            quasar.loading.hide();
            break;
        case "updateSerie":
            quasar.loading.show({
                message: "Mise a jour de la série en cours ...",
            });
            seriesListLoaded.value[serieIndex.value] = serieSelected.value;
            await updateSerieToBackEnd(serieSelected.value);
            formUpdateSerie.value = false;
            quasar.loading.hide();
            break;
        case "deleteSerie":
            quasar.loading.show({
                message: "Suppression de la série en cours ...",
            });
            seriesListLoaded.value[serieIndex.value] = serieSelected.value;
            await deleteSerieToBackEnd(serieSelected.value);
            formDeleteSerie.value = false;
            quasar.loading.hide();
            break;
        default:
            console.log("le formulaire n'est pas détectée");
            break;
    }
    serie.value = {};
    serieName.value = "";

    await loadSeriesWithGenres();
}
async function createSeries(movies) {
    quasar.loading.show({
        message: "Enregistrement des series en cours ...",
    });
    await createSeriesToBackEnd(movies);
    serie.value = {};
    serieName.value = "";
    formAddSeries.value = false;
    seriesList.value.length = 0;
    createserie.value = false;
    await loadSeriesWithGenres();
    quasar.loading.hide();
}
async function createSeriesToBackEnd(series) {
    // Init FormDatata pour envoyer les datas
    const formData = new FormData();
    series.forEach((serie, index) => {
        formData.append(
            `seriesList[${index}][id_serie]`,
            parseInt(serie.id_serie)
        );
        formData.append(`seriesList[${index}][name]`, serie.name);
        formData.append(`seriesList[${index}][synopsis]`, serie.synopsis);
        formData.append(`seriesList[${index}][url_img]`, serie.url_img);
        serie.genres.forEach((genre, genreIndex) => {
            formData.append(
                `seriesList[${index}][genres][${genreIndex}][id_genre]`,
                parseInt(genre.id_genre)
            );
            formData.append(
                `seriesList[${index}][genres][${genreIndex}][name]`,
                genre.name
            );
        });
    });

    try {
        await axios
            .post(api.url_backend_create_series, formData, {
                headers: {
                    accept: "multipart/form-data",
                },
            })
            .then((response) => {
                return response.data.code;
            })
            .catch((error) =>
                console.log(
                    `Erreur lors de la récupération de datas sur le film \n ${error}`
                )
            );
    } catch (error) {
        return error.message;
    }
}

async function updateSerieToBackEnd(serie) {
    const url = `${api.url_backend_update_serie}/${serieIdOrigin.value}`;

    // Init FormData pour envoyer les datas
    const formData = new FormData();
    formData.append(`id_serie`, parseInt(serie.id_serie));
    formData.append(`name`, serie.name);
    formData.append(`synopsis`, serie.synopsis);
    formData.append(`url_img`, serie.url_img);
    serie.genres.forEach((genre, genreIndex) => {
        formData.append(
            `genres[${genreIndex}][id_genre]`,
            parseInt(genre.id_genre)
        );
        formData.append(`genres[${genreIndex}][name]`, genre.name);
    });
    try {
        await axios
            .post(url, formData, {
                headers: {
                    Accept: "multipart/form-data",
                },
            })
            .then((response) => {
                return response.data.code;
            })
            .catch((error) =>
                console.log(
                    `Erreur lors de la mise a jour de datas sur le film \n ${error}`
                )
            );
    } catch (error) {
        return error.message;
    }
}

async function deleteSerieToBackEnd(serie) {
    const url = `${api.url_backend_delete_serie}/${serieIdOrigin.value}`;

    // Init FormDatato pour envoyer les datas
    const jsonData = {
        id_serie: parseInt(serie.id),
    };
    jsonData["genres"] = [];

    serie.genres.forEach((genre) => {
        jsonData["genres"].push({
            id_genre: parseInt(genre.id_genre),
            name: genre.name,
        });
    });
    try {
        await axios
            .post(url, jsonData, {
                headers: {
                    Accept: "applicaton/json",
                },
            })
            .then((response) => {
                return response.data.code;
            })
            .catch((error) =>
                console.log(
                    `Erreur lors de la suppression des datas sur le film \n ${error}`
                )
            );
    } catch (error) {
        return error.message;
    }
}

function showFormulary(serie, index, icon) {
    serieIdOrigin.value = serie.id;
    serieSelected.value = { ...serie };
    serieIndex.value = index;
    serieName.value = serie.name;

    if (icon === "edit") {
        formUpdateSerie.value = true;
    } else {
        formDeleteSerie.value = true;
    }
}

function reset() {
    serieName.value = "";
    seriesList.value.length = 0;
    serie.value = {};
    formAddSeries.value = false;
    formUpdateSerie.value = false;
    formDeleteSerie.value = false;
}

function onReset(mode) {
    serieName.value = "";
    serie.value = {};
    switch (mode) {
        case "addSeries":
            seriesList.value.length = 0;
            formAddSeries.value = false;
            break;
        case "updateSerie":
            formUpdateSerie.value = false;
            break;

        default:
            formDeleteSerie.value = false;
            break;
    }
}

const filteredSeries = computed(() => {
    return seriesListLoaded.value.filter((serie) =>
        serie.name.toLowerCase().includes(props.search.toLowerCase())
    );
});
</script>

<template>
    <!-- Menu Affichage Ecran Petit -->
    <div v-if="quasar.screen.xs" class="bg-dark">
        <div
            v-if="filteredSeries.length > 0"
            class="flex justify-around q-my-sm"
        >
            <q-btn
                color="secondary"
                icon="note_add"
                @click="formAddSeries = true"
                title="Ajouter une serie"
            />
            <q-btn
                class="q-ml-sm q-mr-sm"
                color="green-9"
                @click="editMode = !editMode"
                icon="edit_square"
                title="Mode edition"
            />
            <q-btn
                color="secondary"
                icon="manage_search"
                title="Sélection par genres"
            >
                <q-menu max-height="130px">
                    <div v-for="genre in genresListLoaded" key="genre.id">
                        <q-list dense>
                            <q-item clickable v-close-popup>
                                <q-item-section>
                                    <q-item-label
                                        @click="showSeriesWithGenres(genre)"
                                        >{{ genre.name }}</q-item-label
                                    >
                                </q-item-section>
                            </q-item>
                        </q-list>
                    </div>
                </q-menu>
            </q-btn>
        </div>
        <div v-else>
            <q-btn
                color="secondary"
                icon="note_add"
                @click="formAddSeries = true"
            />
        </div>

        <q-scroll-area style="width: 100vw; height: 80vh" class="bg-dark">
            <div class="flex justify-center wrap" style="gap: 10px">
                <div
                    v-for="serie in filteredSeries"
                    :key="serie.id"
                    class="column items-center"
                >
                    <div class="flex justify-center" v-show="editMode">
                        <q-btn
                            class="q-ml-sm q-mr-sm"
                            color="deep-purple-8"
                            @click="showFormulary(serie, index, 'edit')"
                            icon="edit"
                        />
                        <q-btn
                            class="q-mtlsm q-mr-sm"
                            color="deep-orange-7"
                            @click="showFormulary(serie, index, 'delete')"
                            icon="delete"
                        />
                    </div>
                    <Media :media="serie" />
                </div>
            </div>
        </q-scroll-area>
    </div>
    <!-- Menu Affichage Ecran Large -->
    <div v-if="!quasar.screen.xs">
        <div v-if="checkSeries === true">
            <q-splitter v-model="splitterModel" dark>
                <template v-slot:before>
                    <q-tabs
                        v-model="tab"
                        vertical
                        class="bg-dark text-teal"
                        style="overflow: auto; height: 88vh"
                    >
                        <div class="flex justify-center items-center q-pa-sm">
                            <div v-if="filteredSeries.length > 0">
                                <div class="flex justify-around q-mt-sm">
                                    <q-btn
                                        color="secondary"
                                        icon="note_add"
                                        @click="formAddSeries = true"
                                    />
                                    <q-btn
                                        class="q-ml-sm q-mr-sm"
                                        color="green-9"
                                        @click="editMode = !editMode"
                                        icon="edit_square"
                                    />
                                </div>
                            </div>
                            <div v-else>
                                <q-btn
                                    color="secondary"
                                    icon="note_add"
                                    @click="formAddSeries = true"
                                />
                            </div>
                        </div>

                        <!-- Réintégration des genres -->
                        <div v-for="genre in genresListLoaded" :key="genre.id">
                            <q-tab
                                :name="genre.name"
                                icon="tv"
                                :label="genre.name"
                                @click="showSeriesWithGenres(genre)"
                            />
                        </div>
                    </q-tabs>
                </template>

                <template v-slot:after>
                    <q-tab-panels
                        v-model="tab"
                        style="overflow: auto; height: 88vh"
                        animated
                        swipeable
                        vertical
                        transition-prev="jump-up"
                        transition-next="jump-up"
                        dark
                    >
                        <!-- Panel "all" -->
                        <q-tab-panel name="all" style="height: 100vh">
                            <div class="row justify-start">
                                <div
                                    v-for="(serie, index) in filteredSeries"
                                    :key="serie.id"
                                >
                                    <div
                                        class="flex justify-center"
                                        v-show="editMode"
                                    >
                                        <q-btn
                                            class="q-ml-sm q-mr-sm"
                                            color="deep-purple-8"
                                            @click="
                                                showFormulary(
                                                    serie,
                                                    index,
                                                    'edit'
                                                )
                                            "
                                            icon="edit"
                                        />
                                        <q-btn
                                            class="q-mtlsm q-mr-sm"
                                            color="deep-orange-7"
                                            @click="
                                                showFormulary(
                                                    serie,
                                                    index,
                                                    'delete'
                                                )
                                            "
                                            icon="delete"
                                        />
                                    </div>
                                    <Media :media="serie" />
                                </div>
                            </div>
                        </q-tab-panel>

                        <!-- Panel de genre dynamique -->
                        <q-tab-panel :name="panelGenre" style="height: 100vh">
                            <div class="row justify-start">
                                <div
                                    v-for="(serie, index) in seriesListLoaded"
                                    :key="serie.id"
                                >
                                    <div
                                        class="flex justify-center"
                                        v-show="editMode"
                                    >
                                        <q-btn
                                            class="q-ml-sm q-mr-sm"
                                            color="deep-purple-8"
                                            @click="
                                                showFormulary(
                                                    serie,
                                                    index,
                                                    'edit'
                                                )
                                            "
                                            icon="edit"
                                        />
                                        <q-btn
                                            class="q-mtlsm q-mr-sm"
                                            color="deep-orange-7"
                                            @click="
                                                showFormulary(
                                                    serie,
                                                    index,
                                                    'delete'
                                                )
                                            "
                                            icon="delete"
                                        />
                                    </div>
                                    <Media :media="serie" />
                                </div>
                            </div>
                        </q-tab-panel>
                    </q-tab-panels>
                </template>
            </q-splitter>
        </div>
        <div v-else>
            <div class="bg-dark text-white" style="width: 100vw; height: 100vh">
                <div class="flex justify-center items-center q-pa-sm">
                    <h3>Pour commencer, cliquez sur le bouton</h3>
                </div>
                <div class="flex justify-center items-center q-pa-sm">
                    <q-btn
                        class="q-pa-sm align-center text-h5"
                        color="secondary"
                        @click="formAddSeries = true"
                        title="Ajouter une serie"
                        >Ajouter une serie
                    </q-btn>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire d'ajout de film -->
    <div v-if="quasar.screen.xs">
        <q-dialog v-model="formAddSeries" persistent>
            <div class="bg-white column q-gutter-sm">
                <div class="row justify-between q-ml-sm q-mr-sm">
                    <q-input
                        v-model="serieName"
                        label="Saisir le nom de la serie"
                        autofocus
                    />
                    <q-btn
                        color="secondary q-mt-sm"
                        icon="search"
                        @click="getSerieWithGenre(serieName)"
                    />
                </div>
                <div v-if="serieSearched.id_serie">
                    <Media :media="serieSearched" />
                </div>
                <div class="row justify-start q-gutter-sm">
                    <q-btn label="Annuler" class="text-dark" @click="reset()" />
                    <q-btn
                        label="Ajouter Serie"
                        class="bg-primary text-white"
                        @click="AddSerie(serieSearched)"
                    />
                </div>
                <q-carousel
                    transition-prev="slide-right"
                    transition-next="slide-left"
                    swipeable
                    animated
                    v-model="slide"
                    control-color="primary"
                    navigation-icon="radio_button_unchecked"
                    navigation
                    padding
                    height="200px"
                    class="bg-white q-ma-lg shadow-1 rounded-borders"
                >
                    <q-carousel-slide
                        v-for="(serie, index) in seriesList"
                        :key="serie.id"
                        :name="index"
                        class="column no-wrap flex-center"
                    >
                        <Media :media="serie" />
                    </q-carousel-slide>
                </q-carousel>
                <q-btn
                    v-show="createserie"
                    class="q-ma-xs"
                    color="secondary"
                    @click="createSeries(seriesList)"
                    title="Enregistrer le/les series"
                    >Enregistrer la liste
                </q-btn>
            </div>
        </q-dialog>
    </div>
    <div v-if="!quasar.screen.xs">
        <q-dialog v-model="formAddSeries" persistent full-width full-height>
            <div class="row bg-white q-pa-md">
                <div class="col-4">
                    <div class="row justify-between q-ml-sm q-mr-sm">
                        <q-input
                            v-model="serieName"
                            label="Saisir le nom de la serie"
                            autofocus
                        />
                        <q-btn
                            color="secondary q-mt-sm"
                            icon="search"
                            @click="getSerieWithGenre(serieName)"
                        />
                    </div>
                    <div v-if="serieSearched.id_serie">
                        <q-card-section>
                            <Media :media="serieSearched" />
                        </q-card-section>
                    </div>
                    <q-btn label="Annuler" class="text-dark" @click="reset()" />
                    <q-btn
                        label="Ajouter Serie"
                        class="bg-primary text-white"
                        @click="AddSerie(serieSearched)"
                    />
                </div>
                <div class="col-8">
                    <Form
                        :mode="'addSeries'"
                        @submit="onSubmit('addSeries')"
                        @reset="onReset('addSeries')"
                    >
                        <div v-if="seriesList.length > 0">
                            <div class="row justify-start">
                                <div
                                    v-for="(serie, index) in seriesList"
                                    :key="serie.id"
                                >
                                    <Media :media="serie" />
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p>Aucune series n'est ajoutée</p>
                        </div>
                    </Form>
                </div>
            </div>
        </q-dialog>
    </div>

    <!-- Formulaire de mise à jour d'un film -->
    <q-dialog v-model="formUpdateSerie" persistent>
        <div class="column">
            <div class="bg-white row justify-between q-pa-sm">
                <q-input v-model="serieName" autofocus />
                <q-btn
                    color="secondary q-mt-sm"
                    icon="search"
                    @click="getSerieWithGenre(serieName)"
                />
                <Form
                    :mode="'updateSerie'"
                    @submit="onSubmit('updateSerie')"
                    @reset="onReset('updateSerie')"
                >
                    <Media :media="serieSelected" />
                </Form>
            </div>
        </div>
    </q-dialog>

    <!-- Formulaire de suppression d'un film -->
    <q-dialog v-model="formDeleteSerie" persistent>
        <div class="column bg-white">
            <Form
                :mode="'deleteSerie'"
                @submit="onSubmit('deleteSerie')"
                @reset="onReset('deleteSerie')"
            >
                <Media :media="serieSelected" />
            </Form>
        </div>
    </q-dialog>
</template>

<style></style>
