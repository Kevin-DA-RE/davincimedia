<template>
  <div>

      <q-uploader
      field-name="video"
      url="/api/movie/upload-movie"
      multiple
    ></q-uploader>

  </div>
</template>

<script setup>

</script>

<style>
#qImg {
  width: "600px";
}
</style>
