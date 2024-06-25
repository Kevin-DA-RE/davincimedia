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
      <ListMovies
        v-for="movie in moviesList"
        :movie="movie"
        :key="movie.id"
        :getMovieWithGenre="getMovieWithGenre"
      />
    </div>
  </div>
</template>
<script>
import axios from "axios";
import ListMovies from "./component/ListMovies.vue";
import { ref } from "vue";
export default {
  components: {
    ListMovies,
  },
  data() {
    return {
      name: "",
      apiTMDB: {
        token:
          "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
        url_movies: "https://api.themoviedb.org/3/search/movie",
        url_genres: "https://api.themoviedb.org/3/genre/movie/list?language=fr",
      },
      urlImgComplete: "",
      moviesList: ref([]),
      movieUpdated: {},
      visible: false,
      prompt: false,
    };
  },
  methods: {
    async sendApi(files) {
      if (this.visible == false) {
        this.visible = true;
      }
      const extension = [".mp4", ".mkv", ".avi", ".mpeg", ".mpg"];

      files.forEach((file) => {
        extension.forEach(async (ext) => {
          if (file.name.includes(ext)) {
            var name = file.name.split(ext)[0];
            const data = await this.getMovieWithGenre(name);
            this.moviesList.push(data);
          }
        });
      });
    },

    // Recherche du film et de son/ses genre(s)
    async getMovieWithGenre(name) {
      const movieData = await this.getMovie(name);
      if (!movieData) {
        return `Le film n'a pas été trouvé`;
      }

      const genreData = await this.getGenre(movieData.genre_id);
      if (!genreData) {
        return `Le genre n'a pas été trouvé`;
      }

      movieData.genre_name = genreData;
      return movieData;
    },

    //Recherche du film
    async getMovie(name) {
      /**
       * Recuperation data movie
       */
      var movie = await axios
        .get(`${this.apiTMDB.url_movies}`, {
          params: {
            query: name,
            include_adult: false,
            language: "fr-FR",
            page: 1,
          },
          headers: {
            Authorization: `Bearer ${this.apiTMDB.token}`,
            accept: "application/json",
            "Content-Type": "application/json",
          },
        })
        .then((movie) => movie.data.results)
        .catch((error) =>
          console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`)
        );

      var urlImg = movie[0].poster_path;
      this.urlImgComplete = `https://image.tmdb.org/t/p/original${urlImg}`;
      return {
        id: movie[0].id,
        name: movie[0].title,
        synopsis: movie[0].overview,
        url_img: this.urlImgComplete,
        genre_id: movie[0].genre_ids,
        genre_name: [],
      };
    },

    async getGenre(arrayId) {
      /**
       * Recuperation data category
       */

      var category = await axios
        .get(`${this.apiTMDB.url_genres}`, {
          params: {
            query: "",
          },
          headers: {
            Authorization: `Bearer ${this.apiTMDB.token}`,
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
    },
    sendMovies() {
      /**
       * Envoi data pour créer un film
       */
      axios
        .post("http://127.0.0.1:8000/movie/get-information", this.moviesList, {
          headers: {
            accept: "application/json",
            "Content-Type": "application/json",
          },
        })
        .then((movie) => console.log(movie.data))
        .catch((e) => `Erreur lors de la récupération de données \n ${e}`);
    },
    reset() {
      this.name = "";
    },
  },
  mounted() {
    /*
              axios.get("http://127.0.0.1:8000/movie/get-movie")
              .then(r => {
                console.log(r.data);
                if (lengtResponse > 0)
                {
                  this.visible = true;
                  this.movies = r.data;
                } else {
                  this.visible = false;
                  this.movies = [];
                }
              })
              .catch( e => {
                  console.log(`erreur lors de la recuperation des données : \n ${e}`);
              })
              */
  },
};
</script>
<style>
#qImg {
  width: "600px";
}
</style>
