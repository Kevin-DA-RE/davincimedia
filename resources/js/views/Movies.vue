<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import Movie from "./component/Movie.vue";

const moviesList = ref([])
const genresList =ref([])
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
                        moviesList.value.push(movies)
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
                        genresList.value.push(genres)
                  }
      } catch (error) {
            console.log(error);
      }

    btnMovie.value = (moviesList.value.length < 0) ? false: true;
      
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

</script>

<template>
  <q-page>
    <div v-if="moviesList.length > 0">
      <q-card class="my-card" style="width: 50vh">
        <Movie :movie="movie" />
      </q-card>
    </div>
    <div v-else>
      <q-btn color="secondary" @click="dialogMovie = true" label="Ajouter film" v-show="btnMovie" />
      <q-dialog persistent v-model="dialogMovie" class="text-white" >
        <q-form style="width: 450px;"
          @submit=""
          @reset=""
        >
          <div class="text-h6">Saisir le nom du film</div>
          <q-input
                v-model="movieName"
                @change="getMovieWithGenre(movieName)"
                autofocus
                />
            <div>
              <q-card-section>                  
                  <Movie :movie="movieCreated" />
              </q-card-section>

              <q-btn v-close-popup label="Submit" type="submit" color="primary"/>
              <q-btn v-close-popup label="Reset" type="reset" color="primary" />
            </div>
        </q-form>
          
      </q-dialog>
    </div>
  </q-page>
  
</template>

<style>
</style>