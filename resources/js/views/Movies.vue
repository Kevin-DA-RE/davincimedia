<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import Movie from "./component/Movie.vue";

const moviesList = ref([])
const moviesListLoaded = ref([])
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


const api = {
  tokenApiTMDB:
    "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
  url_movies: "https://api.themoviedb.org/3/search/movie",
  url_genres: "https://api.themoviedb.org/3/genre/movie/list?language=fr",
  url_backend_create_movie: "http://127.0.0.1:8000/api/movie/create-movie",
  url_backend_update_movie: "http://127.0.0.1:8000/api/movie/update-movie",
  url_backend_delete_movie: "http://127.0.0.1:8000/api/movie/delete-movie",
  url_backend_show_movie_movie: "http://127.0.0.1:8000/api/movie/show-movie",
  url_backend_show_movie_genre: "http://127.0.0.1:8000/api/movie/show-genre",
};

async function showMovies(){
  try {
  const movies = await axios
                  .get(api.url_backend_show_movie_movie, {
                        headers: {
                        accept: "application/json",
                        },
              })

  moviesListLoaded.value = movies.data.data
            }
            catch (error) {
        console.log(error);
  }
}

async function showGenres(){
  try {
    const genres = await axios
                  .get(api.url_backend_show_movie_genre, {
                        headers: {
                        accept: "application/json",
                        },
                  })
  genresListLoaded.value = genres.data.data
            }
            catch (error) {
        console.log(error);
  }
}

onMounted( async() => {
      try {
        await showMovies()
      } catch (error) {
        console.log("error showMovie"+error);
      }

      try {
        await showGenres()
      } catch (error) {
        console.log("error ShowGenre"+error);
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
    const genre = await getGenre(movie[0].genre_ids)

    return {
      id: movie[0].id,
      name: movie[0].title,
      synopsis: movie[0].overview,
      url_img: urlImgCompconpleted,
      genre

    };
  } else {
    console.log(" aucun film");
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
  var genre = [];
  arrayId.forEach((array) => {

    categories.forEach((category) => {
      if (array === category.id) {
        genre.push({
            "id_genre": category.id,
            "name": category.name
        })
      }
    });
  });

  return genre;
}

function AddMovie(movie) {
moviesList.value.push(movie)
}

async function onSubmit() {

// Init FormDatato pour envoyer les datas
const formData = new FormData()
moviesList.value.forEach((movie, index) => {

    formData.append(`moviesList[${index}][id_movie]`, parseInt(movie.id))
    formData.append(`moviesList[${index}][name]`, movie.name)
    formData.append(`moviesList[${index}][synopsis]`, movie.synopsis)
    formData.append(`moviesList[${index}][url_img]`, movie.url_img)
    movie.genre.forEach((genre, genreIndex) => {
        formData.append(`moviesList[${index}][genre][${genreIndex}][id_genre]`, parseInt(genre.id_genre))
        formData.append(`moviesList[${index}][genre][${genreIndex}][name]`, genre.name)

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
}

async function updateMovieToBackEnd(movie) {
const url = `${api.url_backend_update_movie}/${movieIdOrigin.value}`
// Init FormDatato pour envoyer les datas
const formData = new FormData()
    formData.append(`id_movie`, parseInt(movie.id))
    formData.append(`name`, movie.name)
    formData.append(`synopsis`, movie.synopsis)
    formData.append(`url_img`, movie.url_img)
    movie.genre.forEach((genre, genreIndex) => {
        formData.append(`genre[${genreIndex}][id_genre]`, parseInt(genre.id_genre))
        formData.append(`genre[${genreIndex}][name]`, genre.name)

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
      console.log("envoi du formulaire de creation");
      movieName.value = ""
      formAddMovies.value = false
    } else {
      console.log("envoi du formulaire de modification");
      movieName.value = ""
      formUpdateMovie.value = false
    }
  }

  await showMovies()
}


async function deleteMovieToBackEnd(movie) {
const url = `${api.url_backend_delete_movie}/${movieIdOrigin.value}`

// Init FormDatato pour envoyer les datas
const formData = new FormData()
    formData.append(`id_movie`, parseInt(movie.id))
    formData.append(`name`, movie.name)
    formData.append(`synopsis`, movie.synopsis)
    formData.append(`url_img`, movie.url_img)
    movie.genre.forEach((genre, genreIndex) => {
        formData.append(`genre[${genreIndex}][id_genre]`, parseInt(genre.id_genre))
        formData.append(`genre[${genreIndex}][name]`, genre.name)

    })


await axios.post(url, formData, {
                          headers: {
                          Accept: "multipart/form-data"
                        }})
                      .then((response) => {return response.data.code}
                      )
                      .catch((error) =>
                        console.log(`Erreur lors de la suppression des datas sur le film \n ${error}`)
                      );
  formDeleteMovie.value = false
  await showMovies()
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

function onReset(){
  moviesList.length = 0
  movieCreated.value = {}
  formAddMovies.value = false
}
</script>

<template>
  <q-page class="">
    <q-btn color="secondary" @click="formAddMovies = true" label="Ajouter film"/>
    <div class="row justify-start">
        <div
        v-for="(movie, index) in moviesListLoaded" :key="movie.id"
        >
        <q-btn color="deep-purple-8" @click="showFormUpdateMovie(movie, index)">Modifier</q-btn>
        <q-btn color="deep-orange-7" @click="showFormDeleteMovie(movie, index)">Supprimer</q-btn>
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
            <q-btn flat label="Modifier Film" @click="deleteMovie()" />
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
                            <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="50px" viewBox="0 0 24 24" width="50px" fill="#000000"><g><rect fill="none" height="24" width="24"/><g><path d="M19,5v14H5V5H19 M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3L19,3z"/></g><path d="M14,17H7v-2h7V17z M17,13H7v-2h10V13z M17,9H7V7h10V9z"/></g></svg>
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
  </q-page>
</template>

<style>
</style>
