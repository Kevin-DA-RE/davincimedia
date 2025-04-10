<script setup>
import axios from "axios";
import { onMounted, ref,computed } from "vue";
import Movie from "./component/Movie.vue";
import Form from "../views/slot/Form.vue";
import { useQuasar } from 'quasar';

const quasar = useQuasar();
const carouselSlide = ref(0)
const moviesList = ref([])
const moviesListLoaded = ref([])
const moviesListFiltred = ref([])
const genresListLoaded =ref([])
const formAddMovies = ref(false)
const formUpdateMovie = ref(false)
const formDeleteMovie = ref(false)
const search = ref("")
const editMode = ref(false)
const movieName = ref("");
const movie = ref({})
const movieSearched = ref({})
const movieSelected = ref({})
const movieIdOrigin = ref()
const movieIndex = ref()
const tab = ref('all')
const splitterModel = ref(20)
const panelGenre = ref("")


const api = {
  url_backend_create_movie: "http://127.0.0.1:8000/api/movie/create-movie",
  url_backend_update_movie: "http://127.0.0.1:8000/api/movie/update-movie",
  url_backend_delete_movie: "http://127.0.0.1:8000/api/movie/delete-movie",
  url_backend_show_movies: "http://127.0.0.1:8000/api/movie/show-movies",
  url_backend_show_genres: "http://127.0.0.1:8000/api/movie/show-genres",
  url_backend_show_movies_genres: "http://127.0.0.1:8000/api/movie/show-movies-genres",
  url_backend_get_movies_genres: "http://127.0.0.1:8000/api/movie/get-movies-with-genres"
};

onMounted( async() => {
      try {
        await loadMoviesWithGenres()
      } catch (error) {
        console.log("error onMounted"+error);
      }
})


async function loadMoviesWithGenres(){
  await showMovies()
  await showGenres()
}


async function showMovies(){
  try {
        const movies = await axios
                  .get(api.url_backend_show_movies, {
                        headers: {
                        accept: "application/json",
                        },
              })

        moviesListLoaded.value = movies.data
            }
            catch (error) {
                return error.response.data
  }
}

async function showGenres(){
  try {
        const genres = await axios
                  .get(api.url_backend_show_genres, {
                        headers: {
                        accept: "application/json",
                        },
              })
              genres.data.unshift({
                "id": 0,
                "name": "all"
              })
              genresListLoaded.value = genres.data
            }
            catch (error) {
            return error.response.data
  }
}

async function showMoviesWithGenres(genre) {
  if (genre.id ===0) {
    await showMovies()
  } else {
    const url = `${api.url_backend_show_movies_genres}/${genre.id}`
    panelGenre.value = genre.name
    const moviesWithGenres = await axios
                                .get(url, {"id": genre.id},{
                                headers: {
                                    accept: "application/json",
                                },
                                })
                                .then((movie) => movie.data)
                                .catch((error) => {
                                  return error.response.data;
                                });
                                moviesListFiltred.value = moviesWithGenres
  }

}

// Recherche du film et de son/ses genre(s)
async function getMovieWithGenre(name) {
  const url = `${api.url_backend_get_movies_genres}/${name}`
  if (formAddMovies.value) {
    return movieSearched.value = await axios.get(url)
                      .then((movie) => movie.data)
                      .catch((error) =>{
                        return error.response.data
                      });
  }else {
    return movieSelected.value = await axios.get(url)
                      .then((movie) => movie.data)
                      .catch((error) =>{
                        return error.response.data
                      });

  }
}

function AddMovie(movie) {
    moviesList.value.push(movie)
    movieSearched.value = {}
    movieName.value = ""
}

async function onSubmit(form) {
    switch (form) {
        case 'addMovies':
            await createMoviesToBackEnd(moviesList.value)
            moviesList.value.length = 0
            formAddMovies.value = false
            break;
        case 'updateMovie':
            moviesListLoaded.value[movieIndex.value] =  movieSelected.value
            await updateMovieToBackEnd(movieSelected.value)
            formUpdateMovie.value = false
            break;
        case 'deleteMovie':
            moviesListLoaded.value[movieIndex.value] =  movieSelected.value
            await deleteMovieToBackEnd(movieSelected.value)
            formDeleteMovie.value = false
            break;
        default:
            console.log("le formulaire n'est pas détectée");
            break;
    }
    movie.value = {}
    movieName.value = ""

    await loadMoviesWithGenres()
}

async function createMoviesToBackEnd(movies){
// Init FormDatata pour envoyer les datas
const formData = new FormData()
movies.forEach((movie, index) => {
    formData.append(`moviesList[${index}][id_movie]`, parseInt(movie.id_movie))
    formData.append(`moviesList[${index}][name]`, movie.name)
    formData.append(`moviesList[${index}][synopsis]`, movie.synopsis)
    formData.append(`moviesList[${index}][url_img]`, movie.url_img)
    movie.genres.forEach((genre, genreIndex) => {
        formData.append(`moviesList[${index}][genres][${genreIndex}][id_genre]`, parseInt(genre.id_genre))
        formData.append(`moviesList[${index}][genres][${genreIndex}][name]`, genre.name)

    })
});

return await axios.post(api.url_backend_create_movie, formData, {
                          headers: {
                          accept: "multipart/form-data"
                        }})
                      .then((response) => {return response.data.code}
                      )
                      .catch((error) =>
                        console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
                      );
}

async function updateMovieToBackEnd(movie) {
const url = `${api.url_backend_update_movie}/${movieIdOrigin.value}`

// Init FormData pour envoyer les datas
const formData = new FormData()
    formData.append(`id_movie`, parseInt(movie.id_movie))
    formData.append(`name`, movie.name)
    formData.append(`synopsis`, movie.synopsis)
    formData.append(`url_img`, movie.url_img)
    movie.genres.forEach((genre, genreIndex) => {
        formData.append(`genres[${genreIndex}][id_genre]`, parseInt(genre.id_genre))
        formData.append(`genres[${genreIndex}][name]`, genre.name)

    })


return await axios.post(url, formData, {
                          headers: {
                          Accept: "multipart/form-data"
                        }})
                      .then((response) => {return response.data.code}
                      )
                      .catch((error) =>
                        console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
                      );
}


async function deleteMovieToBackEnd(movie) {
    const url = `${api.url_backend_delete_movie}/${movieIdOrigin.value}`

    // Init FormDatato pour envoyer les datas
    const jsonData = {
        "id_movie": parseInt(movie.id)
    }
    jsonData["genres"]= []

    movie.genres.forEach((genre) => {
        jsonData["genres"].push({
            "id_genre":parseInt(genre.id_genre),
            "name" : genre.name
        })
    })

    await axios.post(url, jsonData, {
                            headers: {
                            Accept: "applicaton/json"
                            }})
                        .then((response) => {return response.data.code}
                        )
                        .catch((error) =>
                            console.log(`Erreur lors de la suppression des datas sur le film \n ${error}`)
                        );

}

function showFormulary(movie, index, icon){
    movieIdOrigin.value =movie.id
    movieSelected.value = {...movie}
    movieIndex.value = index
    movieName.value = movie.name

    if (icon === 'edit') {
        formUpdateMovie.value = true
    } else {
        formDeleteMovie.value = true
    }

}

function reset(){
  movieName.value = ""
  moviesList.value.length = 0
  movie.value = {}
  formAddMovies.value = false
  formUpdateMovie.value = false
  formDeleteMovie.value = false
}


function onReset(mode){
    movieName.value = ""
    movie.value = {}
    switch (mode) {
        case 'addMovies':
        moviesList.value.length = 0
        formAddMovies.value = false
            break;
        case 'updateMovie':
        formUpdateMovie.value = false
            break;

        default:
        formDeleteMovie.value = false
            break;
    }
}

const filteredMovies = computed(() => {
  return moviesListLoaded.value.filter(movie =>
    movie.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

</script>


<template>
    <!-- Menu Affichage Ecran Petit -->
<div v-if="quasar.screen.lt.sm" class="bg-dark">
    <div v-if="filteredMovies.length > 0" class="flex justify-around q-mb-sm">
        <q-input dense rounded filled bg-color="white" placeholder="Rechercher..."  v-model="search" style="width: 150px;"/>
        <q-btn color="secondary" icon="note_add" @click="formAddMovies = true" />
        <q-btn class="q-ml-sm q-mr-sm" color="green-9" @click="editMode = !editMode" icon="edit_square"/>
        <q-btn color="secondary" icon="manage_search" >
            <q-menu max-height="130px" >
                <div v-for="genre in genresListLoaded" key="genre.id">
                <q-list dense>
                    <q-item clickable>
                    <q-item-section>
                        <q-item-label @click="showMoviesWithGenres(genre)">{{ genre.name }}</q-item-label>
                    </q-item-section>
                    </q-item>
                </q-list>
                </div>
            </q-menu>
        </q-btn>
    </div>
    <div v-else>
        <q-btn color="secondary" icon="note_add" @click="formAddMovies= true" />
    </div>
    <q-scroll-area style="width:100vw; height: 80vh;" class="bg-dark">
      <div class="flex justify-center  wrap" style="gap: 10px;">
        <div v-for="(movie) in filteredMovies" :key="movie.id" class="column items-center">
          <div class="flex justify-center" v-show="editMode">
            <q-btn class="q-ml-sm q-mr-sm" color="deep-purple-8" @click="showFormulary(movie, index, 'edit')" icon="edit"/>
            <q-btn class="q-mtlsm q-mr-sm" color="deep-orange-7" @click="showFormulary(movie, index, 'delete')" icon="delete"/>
          </div>
          <Movie :movie="movie" />
        </div>
      </div>
    </q-scroll-area>
</div>
<!-- Menu Affichage Ecran Large -->
<div v-if="!quasar.screen.lt.sm">
    <q-splitter
      v-model="splitterModel"
      style="height: 100vh"
      dark
    >

      <template v-slot:before>
        <q-tabs
          v-model="tab"
          vertical
          class="bg-dark text-teal"
        >
        <div class="flex justify-center">
            <div v-if="filteredMovies.length > 0">
            <q-input dense rounded filled bg-color="white" placeholder="Rechercher..." class="q-mr-md" v-model="search" style="width: 150px;"/>
            <q-btn color="secondary" icon="note_add" @click="formAddMovies = true" />
            <q-btn class="q-ml-sm q-mr-sm" color="green-9" @click="editMode = !editMode" icon="edit_square"/>
            </div>
            <div v-else>
                <q-btn color="secondary" icon="note_add" @click="formAddMovies = true" />
            </div>
        </div>
        </q-tabs>

      </template>

      <template v-slot:after>
        <q-tab-panels
          v-model="tab"
          animated
          swipeable
          vertical
          transition-prev="jump-up"
          transition-next="jump-up"
          dark
        >

            <q-tab-panel name="all" style="height: 100vh">
            <div class="row justify-start">
                <div
                v-for="(movie, index) in filteredMovies" :key="movie.id"
                >
                <div class="flex justify-center" v-show="editMode">
                    <q-btn class="q-ml-sm q-mr-sm" color="deep-purple-8" @click="showFormulary(movie, index, 'edit')" icon="edit"/>
                    <q-btn class="q-mtlsm q-mr-sm" color="deep-orange-7" @click="showFormulary(movie, index, 'delete')" icon="delete"/>
                </div>
                    <Movie :movie="movie" />
                </div>
            </div>
            </q-tab-panel>

            <q-tab-panel :name="panelGenre" style="height: 100vh">
                <div class="row justify-start">
                    <div
                    v-for="(movie, index) in moviesListFiltred" :key="movie.id"
                    >
                        <div class="flex justify-center">
                            <q-btn class="q-ml-sm q-mr-sm" color="deep-purple-8" @click="showFormulary(movie, index, 'edit')" icon="edit"/>
                            <q-btn class="q-mtlsm q-mr-sm" color="deep-orange-7" @click="showFormulary(movie, index, 'delete')" icon="delete"/>
                        </div>
                        <Movie :movie="movie" />
                    </div>
                </div>
            </q-tab-panel>
        </q-tab-panels>
        </template>
    </q-splitter>
</div>

<!-- Formulaire d'ajout de film -->
<div v-if="quasar.screen.lt.sm">
    <q-dialog  v-model="formAddMovies" persistent full-width full-height>
        <div class="bg-white column q-gutter-sm">
            <div class="row justify-between q-ml-sm q-mr-sm">
            <q-input
                v-model="movieName"
                label="Saisir le nom du film"
                autofocus
                />
            <q-btn color="secondary q-mt-sm"  icon="search" @click="getMovieWithGenre(movieName)" />

            </div>
            <div v-if="movieSearched.id_movie">
                <Movie :movie="movieSearched" />
            </div>
            <div class="row justify-start q-gutter-sm">
                <q-btn label="Annuler" class="text-dark" @click="reset()"/>
                <q-btn label="Ajouter Film" class="bg-primary text-white" @click="AddMovie(movieSearched)"/>
            </div>
            <Form :mode="'addMovies'" @submit="onSubmit('addMovies')" @reset="onReset('addMovies')" >
                <div v-if="moviesList.length > 0">
                    <q-carousel
                        v-model="carouselSlide"
                        transition-prev="scale"
                        transition-next="scale"
                        swipeable
                        animated
                        control-color="white"
                        navigation
                        arrows
                        height="max-content"
                        class="shadow-1 rounded-borders"
                        >
                        <q-carousel-slide
                            v-for="(movie, index) in moviesList"
                            :key="movie.id"
                            :name="index"
                            class="column no-wrap flex-center"
                        >
                            <Movie :movie="movie" />
                        </q-carousel-slide>
                    </q-carousel>
                </div>
                <div v-else class="text-center">
                    <p>Aucun film n'est ajouté</p>
                </div>
            </Form>
        </div>
    </q-dialog>
</div>
<div v-if="!quasar.screen.lt.sm">
    <q-dialog  v-model="formAddMovies" persistent full-width full-height>
        <div class="row bg-white q-pa-md">
            <div class="col-4">
                <h6 class="text-h6">Saisir le nom du film</h6>
                <q-input
                        v-model="movieName"
                        @change="getMovieWithGenre(movieName)"
                        autofocus
                        />
                    <div v-if="movieSearched.id_movie">
                        <q-card-section>
                        <Movie :movie="movieSearched" />
                    </q-card-section>
                    </div>
                <q-btn label="Annuler" class="text-dark" @click="reset()"/>
                <q-btn label="Ajouter Film" class="bg-primary text-white" @click="AddMovie(movieSearched)"/>
            </div>
            <div class="col-8">
                <Form :mode="'addMovies'" @submit="onSubmit('addMovies')" @reset="onReset('addMovies')">
                    <div v-if="moviesList.length > 0">
                        <div class="row justify-start">
                            <div
                            v-for="(movie, index) in moviesList" :key="movie.id"
                            >
                            <Movie :movie="movie" />
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <p>Aucun film n'est ajouté</p>
                    </div>
                </Form>
            </div>
        </div>
    </q-dialog>
</div>

<!-- Formulaire de mise à jour d'un film -->
<q-dialog  v-model="formUpdateMovie" persistent>
    <div class="column">
        <div class="bg-white row justify-between q-pa-sm">
            <q-input
            v-model="movieName"
            autofocus
            />
            <q-btn color="secondary q-mt-sm"  icon="search" @click="getMovieWithGenre(movieName)" />
        </div>
        <Form :mode="'updateMovie'" @submit="onSubmit('updateMovie')" @reset="onReset('updateMovie')">
            <Movie :movie="movieSelected" />
        </Form>
    </div>
</q-dialog>

<!-- Formulaire de suppression d'un film -->
<q-dialog  v-model="formDeleteMovie" persistent>
    <div class="column">
        <Form :mode="'deleteMovie'" @submit="onSubmit('deleteMovie')" @reset="onReset('deleteMovie')">
            <Movie :movie="movieSelected" />
        </Form>
    </div>
</q-dialog>
</template>

<style>
</style>
