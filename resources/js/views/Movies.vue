<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import Movie from "./component/Movie.vue";

const moviesList = ref([])
const moviesListLoaded = ref([])
const moviesListFiltred = ref([])
const genresListLoaded =ref([])
const formAddMovies = ref(false)
const formUpdateMovie = ref(false)
const formDeleteMovie = ref(false)
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
  tokenApiTMDB:
    "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
  url_movies: "https://api.themoviedb.org/3/search/movie",
  url_genres: "https://api.themoviedb.org/3/genre/movie/list?language=fr",
  url_backend_create_movie: "http://127.0.0.1:8000/api/movie/create-movie",
  url_backend_update_movie: "http://127.0.0.1:8000/api/movie/update-movie",
  url_backend_delete_movie: "http://127.0.0.1:8000/api/movie/delete-movie",
  url_backend_show_movies: "http://127.0.0.1:8000/api/movie/show-movies",
  url_backend_show_genres: "http://127.0.0.1:8000/api/movie/show-genres",
  url_backend_show_movies_genres: "http://127.0.0.1:8000/api/movie/show-movies-genres"
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
        console.log(error);
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
              genresListLoaded.value = genres.data
            }
            catch (error) {
        console.log(error);
  }
}

async function showMoviesWithGenres(genre) {
    const url = `${api.url_backend_show_movies_genres}/${genre.id}`
    panelGenre.value = genre.name
    const moviesWithGenres = await axios
                                .get(url, {"id": genre.id},{
                                headers: {
                                    accept: "application/json",
                                },
                                })
                                .then((movie) => movie.data)
                                .catch((error) =>
                                console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
                                );

                                moviesListFiltred.value = moviesWithGenres
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
  const movie = await getMovie(name);

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

//Recherche du film
async function getMovie(name) {
  /**
   * Recuperation data movie
   */

  const movie = await axios
    .get(api.url_movies, {
      params: {
        query: name,
        include_adult: false,
        language: "fr-FR",
        page: 1,
      },
      headers: {
        Authorization: `Bearer ${api.tokenApiTMDB}`,
        accept: "application/json",
        "Content-Type": "application/json",
      },
    })
    .then((movie) => movie.data.results)
    .catch((error) =>
      console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
    );
  let urlImgCompconpleted = "";

  if (movie.length > 0) {
    console.log(" un film");

    var urlImg = movie[0].poster_path;
    urlImgCompconpleted = `https://image.tmdb.org/t/p/original${urlImg}`;
    const genres = await getGenre(movie[0].genre_ids)

    return {
      id: movie[0].id,
      name: movie[0].title,
      synopsis: movie[0].overview,
      url_img: urlImgCompconpleted,
      genres

    };
  } else {
    return { code: 400, message: "Aucun film correspond à la recherche" };
  }
}


//Recherche du/des genres
async function getGenre(arrayId) {
  /**
   * Recuperation data category
   */

  const categories = await axios
    .get(`${api.url_genres}`, {
      params: {
        query: "",
      },
      headers: {
        Authorization: `Bearer ${api.tokenApiTMDB}`,
        accept: "application/json",
        "Content-Type": "application/json",
      },
    })
    .then((category) => category.data.genres)
    .catch((error) =>
      console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
    );

  // Nous comparons la liste de tous les genres avec ceux identifiés et nous gardons que ceux qui sont indenitifiés par le film
  var genres = [];
  arrayId.forEach((array) => {

    categories.forEach((category) => {
      if (array === category.id) {
        genres.push({
            "id_genre": category.id,
            "name": category.name
        })
      }
    });
  });

  return genres;
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
    formData.append(`moviesList[${index}][id_movie]`, parseInt(movie.id))
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
await showMovies()
await showGenres()
}

async function updateMovieToBackEnd(movie) {
const url = `${api.url_backend_update_movie}/${movieIdOrigin.value}`
// Init FormDatato pour envoyer les datas
const formData = new FormData()
    formData.append(`id_movie`, parseInt(movie.id))
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


</script>

<template>
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
            <q-btn color="secondary" icon="note_add" @click="formAddMovies = true" />
        </div>
          <q-tab name="all" icon="view_list" label="Tous les genres" />
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
              v-for="(movie, index) in moviesListLoaded" :key="movie.id"
              >
              <div class="flex justify-center">
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
                          <div v-if="movieCreated.id">
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
</template>

<style>
</style>
