<script setup>
import axios from "axios";
import { onMounted, ref,computed } from "vue";
import Movie from "./component/Movie.vue";
import { useQuasar } from 'quasar';

const quasar = useQuasar();
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
const movieCreated = ref({})
const movieUpdated = ref({})
const movieDeleted = ref({})
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


onMounted( async() => {
      try {
        await showMovies()
        await showGenres()
      } catch (error) {
        console.log("error onMounted"+error);
      }
})



// Recherche du film et de son/ses genre(s)
async function getMovieWithGenre(name) {
  const url = `${api.url_backend_get_movies_genres}/${name}`
  const movie = await axios.get(url)
                      .then((movie) => movie.data)
                      .catch((error) =>{
                        return error.response.data
                      });
  if (movie.code == 400) {
    return {
      code: 400,
      message: "Problème lors de la récupération de film",
      name: name,
    };
  } else {
    if (formAddMovies.value === true) {
      console.log("movie du formulaire de creation");
      return movieCreated.value = movie
    } else {
      console.log("movie du formulaire de modification");
      return movieUpdated.value = movie
    }
  }
}

function AddMovie(movie) {
    moviesList.value.push(movie)
    movieCreated.value = {}
    movieName.value = ""
}

async function onSubmit() {

// Init FormDatato pour envoyer les datas
const formData = new FormData()
moviesList.value.forEach((movie, index) => {
    formData.append(`moviesList[${index}][id_movie]`, parseInt(movie.id_movie))
    formData.append(`moviesList[${index}][name]`, movie.name)
    formData.append(`moviesList[${index}][synopsis]`, movie.synopsis)
    formData.append(`moviesList[${index}][url_img]`, movie.url_img)
    movie.genres.forEach((genre, genreIndex) => {
        formData.append(`moviesList[${index}][genres][${genreIndex}][id_genre]`, parseInt(genre.id_genre))
        formData.append(`moviesList[${index}][genres][${genreIndex}][name]`, genre.name)

    })
});

const sendToMovies = await axios
                      .post(api.url_backend_create_movie, formData, {
                          headers: {
                          accept: "multipart/form-data"
                        }})
                      .then((response) => {return response.data.code}
                      )
                      .catch((error) =>
                        console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
                      );
if(sendToMovies === 200)
movieName.value = ""
formAddMovies.value = false
moviesList.value.length = 0
await showMovies()
await showGenres()
}

async function updateMovieToBackEnd(movie) {
const url = `${api.url_backend_update_movie}/${movieIdOrigin.value}`
// Init FormDatato pour envoyer les datas
const formData = new FormData()
    formData.append(`id_movie`, parseInt(movie.id_movie))
    formData.append(`name`, movie.name)
    formData.append(`synopsis`, movie.synopsis)
    formData.append(`url_img`, movie.url_img)
    movie.genres.forEach((genre, genreIndex) => {
        formData.append(`genres[${genreIndex}][id_genre]`, parseInt(genre.id_genre))
        formData.append(`genres[${genreIndex}][name]`, genre.name)

    })


const sendToMovie = await axios
                      .post(url, formData, {
                          headers: {
                          Accept: "multipart/form-data"
                        }})
                      .then((response) => {return response.data.code}
                      )
                      .catch((error) =>
                        console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
                      );
  if(sendToMovie === 200){
    if (formAddMovies === true) {
      movieName.value = ""
      formAddMovies.value = false
    } else {
      movieName.value = ""
      formUpdateMovie.value = false
    }
  }

  await showMovies()
  await showGenres()
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

        console.log(jsonData);


    await axios.post(url, jsonData, {
                            headers: {
                            Accept: "applicaton/json"
                            }})
                        .then((response) => {return response.data.code}
                        )
                        .catch((error) =>
                            console.log(`Erreur lors de la suppression des datas sur le film \n ${error}`)
                        );

  formDeleteMovie.value = false
  await showMovies()
  await showGenres()
}

function showFormUpdateMovie(movie, index){
    movieIdOrigin.value =movie.id
    movieUpdated.value = {...movie}
    movieIndex.value = index
    movieName.value = movie.name
    formUpdateMovie.value = true
}

function showFormDeleteMovie(movie, index){
    movieIdOrigin.value =movie.id
    movieDeleted.value = movie
    movieIndex.value = index
    formDeleteMovie.value = true
}

async function updateMovie(){
    moviesListLoaded.value[movieIndex.value] =  movieUpdated.value
    await updateMovieToBackEnd(movieUpdated.value)
    formUpdateMovie.value = false
}

async function deleteMovie(){
    moviesListLoaded.value[movieIndex.value] =  movieDeleted.value
    await deleteMovieToBackEnd(movieDeleted.value)
    formDeleteMovie.value = false
}


function resetAddmovie(){
  movieName.value = ""
  moviesList.value.length = 0
  movieCreated.value = {}
  formAddMovies.value = false
}

function resetUpdateMovie () {
  movieName.value = ""
  movieUpdated.value = {}
  formUpdateMovie.value = false
}

function resetDeleteMovie(){
  movieDeleted.value = {}
  formDeleteMovie.value = false
}


function onReset(){
  moviesList.length = 0
  movieCreated.value = {}
  formAddMovies.value = false
}

const filteredMovies = computed(() => {
  return moviesListLoaded.value.filter(movie =>
    movie.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

</script>


<template>
  <div v-if="quasar.screen.lt.sm" class="bg-dark">
    <div class="flex justify-center">
        <div v-if="filteredMovies.length > 0">
        <q-input dense rounded filled bg-color="white" placeholder="Rechercher..." class="q-mr-md" v-model="search" style="width: 150px;"/>
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
            <q-btn color="secondary" icon="note_add" @click="formAddMovies = true" />
        </div>
    </div>
        <div class="bg-dark row justify-start" style="height: 100vh;">
            <div v-for="(movie, index) in filteredMovies" :key="movie.id">
                <Movie :movie="movie" />
            </div>
        </div>
  </div>
  <div v-else>
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
            <!-- Barre de recherche à droite -->
            <q-input dense rounded filled bg-color="white" placeholder="Rechercher..." class="q-mr-md" v-model="search" />
            <q-btn color="secondary" icon="note_add" @click="formAddMovies = true" />
            <q-btn class="q-ml-sm q-mr-sm" color="green-9" @click="editMode = !editMode" icon="edit_square"/>
        </div>
            <div v-for="genre in genresListLoaded" key="genre.id">
              <q-tab :name="genre.name" icon="movie" :label="genre.name" @click="showMoviesWithGenres(genre)"/>
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
                <q-btn class="q-ml-sm q-mr-sm" color="deep-purple-8" @click="showFormUpdateMovie(movie, index)" icon="edit"/>
                <q-btn class="q-mtlsm q-mr-sm" color="deep-orange-7" @click="showFormDeleteMovie(movie, index)" icon="delete"/>
              </div>
                  <Movie :movie="movie" />
              </div>
          </div>

        <q-dialog v-model="formDeleteMovie">
              <q-card style="min-width: 350px">
                  <q-card-section>
                      <h6 class="text-h6">Voulez-vous supprimer ce film</h6>
                  </q-card-section>

              <q-card-section class="q-pt-none">
                      <Movie :movie="movieDeleted" />
              </q-card-section>

              <q-card-actions align="right" class="text-primary">
                  <q-btn flat label="Annuler" @click="resetDeleteMovie()" />
                  <q-btn flat label="Supprimer le Film" @click="deleteMovie()" />
              </q-card-actions>
              </q-card>
          </q-dialog>
          <q-dialog v-model="formUpdateMovie">
              <q-card style="min-width: 350px">
                  <q-card-section>
                      <h6 class="text-h6">Modifier le film</h6>
                  </q-card-section>

              <q-card-section class="q-pt-none">
                  <q-input
                      v-model="movieName"
                      @change="getMovieWithGenre(movieName)"
                      autofocus
                      />
                      <q-card-section>
                      <Movie :movie="movieUpdated" />
                  </q-card-section>
              </q-card-section>

              <q-card-actions align="right" class="text-primary">
                  <q-btn flat label="Annuler" @click="resetUpdateMovie()" />
                  <q-btn flat label="Modifier Film" @click="updateMovie()" />
              </q-card-actions>
              </q-card>
          </q-dialog>
          <q-dialog  v-model="formAddMovies" persistent full-width full-height>
              <div class="row  bg-white q-pa-md">
                  <div class="col-4">
                      <h6 class="text-h6">Saisir le nom du film</h6>
                      <q-input
                              v-model="movieName"
                              @change="getMovieWithGenre(movieName)"
                              autofocus
                              />
                          <div v-if="movieCreated.id_movie">
                              <q-card-section>
                              <Movie :movie="movieCreated" />
                          </q-card-section>
                          </div>
                      <q-btn label="Ajouter Film" class="bg-primary text-white" @click="AddMovie(movieCreated)"/>
                      <q-btn label="Annuler" class="text-dark" @click="resetAddmovie()"/>
                  </div>
                  <div class="col-8">
                      <q-form
                      @submit.prevent="onSubmit"
                      @reset="onReset"
                      class="q-gutter-md"
                      >
                          <h6>Liste des films a enregistrer</h6>
                          <div v-if="moviesList.length > 0">
                            <div class="row justify-start">
                              <div
                              v-for="(movie, index) in moviesList" :key="movie.id"
                              >
                              <Movie :movie="movie" />
                              </div>
                            </div>
                            <q-btn label="Enregistrer les films" type="submit" color="primary"/>
                            <q-btn label="Annuler" type="reset" />
                          </div>
                          <div v-else>
                              <p>Aucun film n'est ajouté</p>
                          </div>

                      </q-form>
                  </div>
              </div>
          </q-dialog>
        </q-tab-panel>

        <q-tab-panel :name="panelGenre" style="height: 100vh">
            <div class="row justify-start">
              <div
              v-for="(movie, index) in moviesListFiltred" :key="movie.id"
              >
                <div class="flex justify-center">
                  <q-btn class="q-ml-sm q-mr-sm" color="deep-purple-8" @click="showFormUpdateMovie(movie, index)" icon="edit"/>
                  <q-btn class="q-mtlsm q-mr-sm" color="deep-orange-7" @click="showFormDeleteMovie(movie, index)" icon="delete"/>
                </div>
                <Movie :movie="movie" />
              </div>
          </div>
        </q-tab-panel>
      </q-tab-panels>
      </template>

    </q-splitter>
  </div>
</template>

<style>
</style>
