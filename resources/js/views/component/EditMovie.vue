<template>
  <q-btn label="Modifier" color="primary" @click="prompt = true" />
   <q-dialog v-model="prompt" persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Saisir le nom du film</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-input dense v-model="movieTitleChange" @change="changeMovie" autofocus />

          <q-img :src="movieUrlImg" id="qImg">
          <div class="absolute-top text-h6">
              {{ movieTitle }}
              </div>
          </q-img>
      <q-card-section>
          {{ movieSynopsis }}
      </q-card-section>
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Annuler" v-close-popup @click="rest" />
          <q-btn flat label="Valider" v-close-popup @click="submit"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
</template>

<script >
  import axios from 'axios';
export default {
  name: "EditMovie",
  props: {
    movie: Object
  },
  data () {
    return {
      apiTMDB: {
          "token": "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
          "url_movies" : 'https://api.themoviedb.org/3/search/movie',
          "url_genres" : 'https://api.themoviedb.org/3/genre/movie/list?language=fr',
        },
      prompt: false,
      movieTitleChange: this.movie.name,
      movieTitle: this.movie.name,
      movieUrlImg: this.movie.url_img,
      movieSynopsis: this.movie.synopsis
    }
  },
  methods :{
    async changeMovie() {
        const data = await this.getMovieWithGenre(this.movieTitleChange);
        this.movieTitle = data.name;
        this.movieUrlImg = data.url_img;
        this.movieSynopsis = data.synopsis;
      },
      submit(){
        this.movie.name = this.movieTitle,
        this.movie.url_img = this.movieUrlImg,
        this.movieSynopsis = this.movie.synopsis
      },

      // Recherche du film et de son/ses genre(s)
      async getMovieWithGenre(name){
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
      async getMovie(name){
        /**
         * Recuperation data movie
         */
         var movie = await axios.get(`${this.apiTMDB.url_movies}`,{
                                          params: {
                                            query: name,
                                            include_adult: false,
                                            language: 'fr-FR',
                                            page: 1
                                          },
                                        headers:{
                                          Authorization: `Bearer ${this.apiTMDB.token}`,
                                          accept: 'application/json',
                                          'Content-Type': 'application/json'

                                        }})
                                        .then(movie => movie.data.results)
                                        .catch(error => console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`));
              var urlImg = movie[0].poster_path;
              this.urlImgComplete = `https://image.tmdb.org/t/p/original${urlImg}`;
                return  {
                        "id": movie[0].id,
                        "name": movie[0].title,
                        "synopsis": movie[0].overview,
                        "url_img": this.urlImgComplete,
                        "genre_id": movie[0].genre_ids,
                        "genre_name" : []
                      }

        },

        async getGenre(arrayId) {
          /**
         * Recuperation data category
         */

         var category = await axios.get(`${this.apiTMDB.url_genres}`,{
                                          params: {
                                            query: ""
                                          },
                                        headers:{
                                          Authorization: `Bearer ${this.apiTMDB.token}`,
                                          accept: 'application/json',
                                          'Content-Type': 'application/json'

                                        }})
                                        .then(category => category.data.genres)
                                        .catch(error => console.log(`Erreur lors de la récupération de datas sur le film \n ${error}`));
            // Nous comparons la liste de tous les genres avec ceux identifiés et nous gardons que ceux qui sont indenitifiés par le film
            var genre = [];
            arrayId.forEach(id => {
                            category.forEach(ids => {
                                    if (id === ids.id) {
                                      genre.push(ids.name);
                                    }
                                });
                            }
                          );

         return genre.map(item => ({name: item}));
        }
  }
}
</script>
