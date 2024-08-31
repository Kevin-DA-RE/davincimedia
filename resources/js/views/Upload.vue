<template>
    <div  class="q-pa-md" style="max-width: 400px">
        <q-form
            action="/api/movie/upload-movie"
            method="post"
            @submit="sendMovie"
            @reset="resetUpload"
            class="q-pa-md q-ma-md shadow-3"
            enctype="multipart/form-data"
        >
            <q-file
            v-model="video"
            label="Insérer ici votre media"
            color="primary"
            counter
            outlined
            multiple
            @update:model-value ="getInformationWithTMDB"
            />
          <q-btn label="Soumettre" type="submit" color="primary"/>
          <q-btn label="Annuler" type="reset" color="primary" flat class="q-ml-sm" />
            <div v-for="(movie, index) in moviesList" :key="movie.id" class="q-mb-md">
                <div v-show="listingMovie">
                    <q-card class="my-card" style="width: 50vh">
                    <q-btn label="Modifier" color="primary" @click="showDialog(movie, index)" />
                    <div style="width: 100%;">
                        <Movie :movie="movie" />
                    </div>

                    </q-card>
                </div>
            </div>

        </q-form>

                        <!-- dialogMovie pour modifier un film -->

                        <q-dialog v-model="dialogMovie">
                            <q-card style="min-width: 350px">
                            <q-card-section>
                                <div class="text-h6">Saisir le nom du film</div>
                            </q-card-section>

                            <q-card-section class="q-pt-none">
                                <q-input
                                dense
                                v-model="movieTitleChange"
                                @change="changeMovie(movieTitleChange)"
                                autofocus
                                />
                                <Movie :movie="movieUpdated" />
                            </q-card-section>

                            <q-card-actions align="right" class="text-primary">
                                <q-btn flat label="Annuler" v-close-popup @click="reset" />
                                <q-btn flat label="Valider" v-close-popup @click="updateMovie" />
                            </q-card-actions>
                            </q-card>
                        </q-dialog>
                        </div>


</template>

<script setup>
import axios from "axios";
import Movie from "./component/Movie.vue";
import { ref } from "vue";

const api = ref({
  tokenApiTMDB:
    "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
  url_movies: "https://api.themoviedb.org/3/search/movie",
  url_genres: "https://api.themoviedb.org/3/genre/movie/list?language=fr",
  url_backend: "http://127.0.0.1:8000/api/movie/upload-movie"
});

const moviesList = ref([]);
const listingMovie = ref(false);
const movieTitleChange = ref();
const movieIndex = ref();
const movieUpdated = ref({});
let dialogMovie = ref(false);
const uploader = ref(null);
const video = ref()

// Fonction pour upload moviesList
async function getInformationWithTMDB(files) {
  if (listingMovie.value == false) {
    listingMovie.value = true;
  }
  const re = /(\.[^.]+)?$/; // Cible l'extension du film

  const moviesListPromises = files.map(async (movie) => {
    const ext = re.exec(movie.name)[1]; // Extract the extension
    const data = await getMovieWithGenre(movie.name.split(ext)[0]);
    data["movie"]= movie
    moviesList.value.push(data);
  });

  await Promise.all(moviesListPromises);
  console.log(moviesList.value);

}

// Fonctions pour q-dialogMovie
function showDialog(movie, index) {
  if (movie.code === 400) {
    movieIndex.value = index;
    movieTitleChange.value = movie.name;
    movieUpdated.value = {};
    dialogMovie.value = true;
  } else {
    movieIndex.value = index;
    movieTitleChange.value = movie.name;
    movieUpdated.value = { ...movie };
    dialogMovie.value = true;
  }
}

// Fonction pour changer la card du film
async function changeMovie(name) {
  movieUpdated.value = await getMovieWithGenre(name);
}

// Fonction pour mettre à jour la card du film modifie
function updateMovie() {
  moviesList.value[movieIndex.value] = movieUpdated.value;
  dialogMovie.value = false;
}

// Fonction pour reinitialiser les objets movie
function reset() {
  movieUpdated.value = ref({});
}

// Recherche du film et de son/ses genre(s)
async function getMovieWithGenre(name) {
  const movieData = await getMovie(name);

  if (movieData.code == "400") {
    return {
      code: 400,
      message: "Problème lors de la récupération de film",
      name: name,
    };
  } else {
    const genreData = await getGenre(movieData.genre_id);
    movieData.genre_name = genreData;
    return movieData;
  }
}

//Recherche du film
async function getMovie(name) {
  /**
   * Recuperation data movie
   */
  const movie = await axios
    .get(`${api.value.url_movies}`, {
      params: {
        query: name,
        include_adult: false,
        language: "fr-FR",
        page: 1,
      },
      headers: {
        Authorization: `Bearer ${api.value.tokenApiTMDB}`,
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
    var urlImg = movie[0].poster_path;
    urlImgCompconpleted = `https://image.tmdb.org/t/p/original${urlImg}`;
    return {
      id: movie[0].id,
      name: movie[0].title,
      synopsis: movie[0].overview,
      url_img: urlImgCompconpleted,
      genre_id: movie[0].genre_ids,
      genre_name: [],
    };
  } else {
    return { code: 400, message: "Aucun film correspond à la recherche" };
  }
}

async function getGenre(arrayId) {
  /**
   * Recuperation data category
   */

  const category = await axios
    .get(`${api.value.url_genres}`, {
      params: {
        query: "",
      },
      headers: {
        Authorization: `Bearer ${api.value.tokenApiTMDB}`,
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
  arrayId.forEach((id) => {
    category.forEach((ids) => {
      if (id === ids.id) {
        genre.push(ids.name);
      }
    });
  });

  return genre.map((item) => ({ name: item }));
}

function removeFile(file, index) {
    uploader.value.removeFile(file)
    moviesList.value.splice(index, 1)
}

function sendMovie(){
    console.log(`soumission du formulaire`);
    const formData = new FormData();
    formData.append('moviesList', JSON.stringify(moviesList.value));


    axios
    .post(`${api.value.url_backend}`, formData)
    .then((response) => console.log(response.data)
     )
    .catch((error) =>
      console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
    );
}

function resetUpload() {

}
</script>
<style></style>
