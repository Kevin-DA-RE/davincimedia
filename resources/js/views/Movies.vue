<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import Movie from "./component/Movie.vue";

const moviesList = ref([])
const moviesListLoaded = ref([])
const genresList=ref([])
const genresListLoaded =ref([])
const dialogMovie = ref(false)
const movieTitleChange = ref();
const movieIndex = ref();
const movieName = ref("");
const movieCreated = ref({})
const api = {
  tokenApiTMDB:
    "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
  url_movies: "https://api.themoviedb.org/3/search/movie",
  url_genres: "https://api.themoviedb.org/3/genre/movie/list?language=fr",
  url_backend_create_movie: "http://127.0.0.1:8000/api/movie/create-movie",
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
  movieCreated.value = await getMovie(name);

  if (movieCreated.value.code == 400) {
    return {
      code: 400,
      message: "Problème lors de la récupération de film",
      
      name: name,
    };
  } else {
    return movieCreated.value;

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
dialogMovie.value = false
await showMovies()
}


function onReset(){
  movieName.value = ""
  dialogMovie.value = false
}
</script>

<template>
  <q-page class="bg-dark">
    <q-btn color="secondary" @click="dialogMovie = true" label="Ajouter film"/>    
    <div class="row justify-start">      
        <div
        v-for="(movie) in moviesListLoaded" :key="movie.id"
        >
            <Movie :movie="movie" />

        </div>
    </div>
        <q-dialog  v-model="dialogMovie" persistent full-width full-height>
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
                    <q-btn label="Annuler" class="text-dark" @click="onReset()"/>
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
  </q-page>
</template>

<style>
</style>
