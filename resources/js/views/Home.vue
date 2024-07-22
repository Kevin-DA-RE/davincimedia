<template>
  <div>
    <q-form @submit="sendMovies" class="q-gutter-md">
      <q-uploader
        multiple
        label="Téléchargez votre fichier"
        @added="sendApi"
      ></q-uploader>
      <div>
        <q-btn label="Submit" type="submit" color="primary" />
        <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm" />
      </div>
    </q-form>
    <div v-show="visible">
      <div v-for="movie in moviesList" :key="movie.id">
        <q-card class="my-card" style="width: 50vh">
          <q-btn label="Modifier" color="primary" @click="showDialog(movie)" />
          <Movie :movie="movie" />

          <q-dialog v-model="dialog">
            <q-card style="min-width: 350px">
              <q-card-section>
                <div class="text-h6">Saisir le nom du film</div>
              </q-card-section>

              <q-card-section class="q-pt-none">
                <q-input
                  dense
                  v-model="movieTitleChange"
                  @change="changeMovie"
                  autofocus
                />
                <div v-if="movieUpdated.id">
                  <Movie :movie="movieUpdated" />
                </div>
                <div v-else>
                  <Movie :movie="movieOrigin" />
                </div>
              </q-card-section>

              <q-card-actions align="right" class="text-primary">
                <q-btn flat label="Annuler" v-close-popup @click="reset" />
                <q-btn flat label="Valider" v-close-popup @click="updateMovie(movie.id)" />
              </q-card-actions>
            </q-card>
          </q-dialog>

        </q-card>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import Movie from "./component/Movie.vue";
import { ref } from "vue";

let api = ref({
  tokenApiTMDB:
    "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
  tokenApiNLP: "2e37b5e1a4ac8f9d4af69758c05b295183d51337",
  url_movies: "https://api.themoviedb.org/3/search/movie",
  url_genres: "https://api.themoviedb.org/3/genre/movie/list?language=fr",
  url_movie_correction:
    "https://api.nlpcloud.io/v1/gpu/finetuned-llama-3-70b/gs-correction",
});

let moviesList = ref([]);
let visible = ref(false);
let movieTitleChange = ref()
let movieOrigin = ref({});
let movieUpdated = ref({});
let dialog = ref(false)

async function sendApi(files) {
  if (visible.value == false) {
    visible.value = true;
  }
  const extension = [".mp4", ".mkv", ".avi", ".mpeg", ".mpg"];

  files.forEach((file) => {
    extension.forEach(async (ext) => {
      if (file.name.includes(ext)) {
        var name = file.name.split(ext)[0];
        var data = await getMovieWithGenre(name);
        if (data.code == 400) {
          var correction = await correctionMovie(name);
          data = await getMovieWithGenre(correction);
          moviesList.value.push(data);
        } else {
          moviesList.value.push(data);
        }
      }
    });
  });
}
function showDialog(movie) {
    movieTitleChange = movie.name
    movieOrigin.value = ref({...movie})
    dialog.value = true;
}
async function changeMovie() {
  movieUpdated.value = await getMovieWithGenre(movieTitleChange);
}
function updateMovie(index) {
  moviesList[index] =   movieUpdated.value
}
function reset() {

}

// Recherche du film et de son/ses genre(s)
async function getMovieWithGenre(name) {
  const movieData = await getMovie(name);

  if (movieData.code == "400") {
    console.log("pb lors de la recherche d'infos !");
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

async function correctionMovie(name) {
  const param = {
    headers: {
      Authorization: `Token ${api.value.tokenApiNLP}`,
      "Content-Type": "application/json",
    },
  };
  const data = {
    text: name,
  };
  const movie = await axios
    .post(`${api.url_movie_correction}`, data, param)
    .then((movie) => movie.data.correction)
    .catch((error) =>
      console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
    );

  return movie;
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
    let urlImgComplete = ""
    if (movie.length > 0) {
    var urlImg = movie[0].poster_path;
    urlImgComplete = `https://image.tmdb.org/t/p/original${urlImg}`;
    return {
      id: movie[0].id,
      name: movie[0].title,
      synopsis: movie[0].overview,
      url_img: urlImgComplete,
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
function sendMovies() {
  /**
   * Envoi data pour créer un film
   */
  axios
    .post("http://127.0.0.1:8000/movie/get-information", moviesList, {
      headers: {
        accept: "application/json",
        "Content-Type": "application/json",
      },
    })
    .then((movie) => console.log(movie.data))
    .catch((e) => `Erreur lors de la récupération de données \n ${e}`);
}

function mounted() {
  /*
                axios.get("http://127.0.0.1:8000/movie/get-movie")
                .then(r => {
                    console.log(r.data);
                    if (lengtResponse > 0)
                    {
                    visible = true;
                    movies = r.data;
                    } else {
                    visible = false;
                    movies = [];
                    }
                })
                .catch( e => {
                    console.log(`erreur lors de la recuperation des données : \n ${e}`);
                })
                */
}
</script>

<style>
#qImg {
  width: "600px";
}
</style>
