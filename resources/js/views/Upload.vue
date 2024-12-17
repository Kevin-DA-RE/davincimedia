<template>
    <div  class="q-pa-md" style="max-width: 400px">
        <q-form
            action="/api/movie/upload-movie"
            method="post"
            @submit.prevent="sendMovie"
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

const api = {
  tokenApiTMDB:
    "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNTFlMjdhNzVhZTY1ZTNjNDUxNjdlMmVkOTYwMmU3MSIsInN1YiI6IjY1ZThlZmEzM2Q3NDU0MDE3ZGI4MzczNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.GNY6Ryp_gInMIzeoedzI7ooJHMdm1wX9YSTQyODot9s",
  url_movies: "https://api.themoviedb.org/3/search/movie",
  url_genres: "https://api.themoviedb.org/3/genre/movie/list?language=fr",
  url_backend: "http://127.0.0.1:8000/api/movie/upload-movie"
};


const uploader = ref(null);
const video = ref()



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







//suppression du film dans le tableau genere
function removeFile(file, index) {
    uploader.value.removeFile(file)
    moviesList.value.splice(index, 1)
}

//Envoi data vers backend
async function sendMovie(){
    // Init FormDatato pour envoyer les datas
    const formData = new FormData()

    // Construction formulaire avec infos pour envoi data
    moviesList.value.forEach((movie, index) => {

    formData.append(`moviesList[${index}][name]`, movie.name)
    formData.append(`moviesList[${index}][synopsis]`, movie.synopsis)
    formData.append(`moviesList[${index}][url_img]`, movie.url_img)
    formData.append(`moviesList[${index}][file]`, movie.file)

    movie.genre_id.forEach((genre, genreIndex) => {
        formData.append(`moviesList[${index}][genre_id][${genreIndex}][id]`, genre.id)

    })
    movie.genre_name.forEach((genre, nameIndex) => {
        formData.append(`moviesList[${index}][genre_name][${nameIndex}][name]`, genre.name)

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

function resetUpload() {

}
</script>
<style></style>
