<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import Movie from "./component/Movie.vue";

const moviesList = ref([])
const moviesListLoaded = ref([])
const genresList=ref([])
const genresListLoaded =ref([])
const btnMovie = ref(false)
let dialogMovie = ref(false)
const movieTitleChange = ref();
const movieIndex = ref();
const movieName = ref("");
const movieCreated = ref({})
const api = {
  tokenApiTMDB:
    "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
  url_movies: "https://api.themoviedb.org/3/search/movie",
  url_genres: "https://api.themoviedb.org/3/genre/movie/list?language=fr",
  url_backend: "http://127.0.0.1:8000/api/movie/create-movie"
};

onMounted( async() => {
      try {
              const movies = await axios
                  .get(`http://127.0.0.1:8000/api/movie/show-movie`, {
                        headers: {
                        accept: "application/json",
                        },
                  })
                  .then((movie) => movie.data)

                  if (movies.length > 0) {
                        moviesListLoaded.value.push(movies)
                  }

      } catch (error) {
            console.log(error);

      }

      try {
             const genres = await axios
                  .get(`http://127.0.0.1:8000/api/movie/show-genre`, {
                        headers: {
                        accept: "application/json",
                        },
                  })
                  .then((genre) => genre.data)
                  if (genres.length > 0) {
                        genresListLoaded.value.push(genres)
                  }
      } catch (error) {
            console.log(error);
      }

    btnMovie.value = (moviesListLoaded.value.length < 0) ? false: true;

})



// Recherche du film et de son/ses genre(s)
async function getMovieWithGenre(name) {
  movieCreated.value = await getMovie(name);

  if (movieCreated.value.code == "400") {
    return {
      code: 400,
      message: "Problème lors de la récupération de film",
      name: name,
    };
  } else {
    const genreData = await getGenre(movieCreated.value.genre_id);
    movieCreated.value.genre_name = genreData;
    return movieCreated.value;

  }
}

//Recherche du film
async function getMovie(name) {
  /**
   * Recuperation data movie
   */
  const movie = await axios
    .get(`${api.url_movies}`, {
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
    var urlImg = movie[0].poster_path;
    urlImgCompconpleted = `https://image.tmdb.org/t/p/original${urlImg}`;
    const genreId = movie[0].genre_ids.map(id => {
        return {'id': id}
    })
    return {
      id: movie[0].id,
      name: movie[0].title,
      synopsis: movie[0].overview,
      url_img: urlImgCompconpleted,
      genre_id: genreId,
      genre_name: [],
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
  var genre = [];
  arrayId.forEach((array) => {

    categories.forEach((category) => {
      if (array.id === category.id) {
        genre.push(category.name);
      }
    });
  });

  return genre.map((item) => ({ name: item }));
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

    movie.genre_id.forEach((genre, genreIndex) => {
        formData.append(`moviesList[${index}][genre][${genreIndex}][id_genre]`, parseInt(genre.id))

    })
    movie.genre_name.forEach((genre, nameIndex) => {
        formData.append(`moviesList[${index}][genre][${nameIndex}][name]`, genre.name)

    })

});

await axios
    .post(`${api.url_backend}`, formData)
    .then((response) => console.log(response.data)
     )
    .catch((error) =>
      console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
    );

}
</script>

<template>
  <q-page>
    <div v-if="moviesListLoaded.length > 0">
        <div
        class="flex justify-start"
        v-for="(movie, index) in moviesListLoaded" :key="movie.id"
        >
            <Movie :movie="movie" />
        </div>

    </div>
    <div v-else>
      <q-btn color="secondary" @click="dialogMovie = true" label="Ajouter film" v-show="btnMovie" />
        <q-dialog v-model="dialogMovie" persistent full-width>
            <div class="row">
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
                    <q-btn label="Ajouter Film" @click="AddMovie(movieCreated)" color="primary"/>
                    <q-btn label="Annuler" color="danger"/>
                </div>
                <div class="col-8">
                    <q-form
                    @submit.prevent="onSubmit"
                    @reset="onReset"
                    class="q-gutter-md"
                    >
                        <h6>Liste des films a enregistrer</h6>
                        <div v-if="moviesList.length > 0">
                          <div class="row justify-around">
                            <div
                            v-for="(movie, index) in moviesList" :key="movie.id"
                            >
                                <Movie :movie="movie" />
                            </div>
                          </div>
                          <q-btn label="Enregistrer les films" type="submit" color="primary"/>
                          <q-btn label="Annuler" type="reset" color="danger"/>
                        </div>
                        <div v-else>
                            <p>Aucun film n'est ajouté</p>
                        </div>

                    </q-form>
                </div>
            </div>
        </q-dialog>
    </div>
  </q-page>
</template>

<style>
</style>
