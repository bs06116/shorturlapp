
<template>
    <div class="url-shortener">
      <form @submit.prevent="shortenUrl" class="url-form">
        <label for="originalUrl">Original URL:</label>
        <input v-model="originalUrl" type="text" id="originalUrl" required>
        <button type="submit">Submit</button>
      </form>
      <div class="result-container">
        <p v-if="shortenedUrl" class="success-message">
            <span v-if="isNew==0">Short URL already created of againt this URl</span><span v-else>Short URL</span>: <a :href="shortenedUrl" target="_blank">{{ shortenedUrl }}</a>
        </p>
        <p v-if="error" class="error-message">Error: {{ error }}</p>
      </div>
    </div>
  </template>
  
 
  

<script>
export default {
    data() {
        return {
            originalUrl: '',
            shortenedUrl: '',
            isNew: '',
            error: '',
        };
    },
    methods: {
        shortenUrl() {
            this.shortenedUrl = '';
            this.error = '';
            axios.post('/shorten-url', { original_url: this.originalUrl })
                .then(response => {
                    this.shortenedUrl = response.data.short_url;
                    this.isNew = response.data.is_new
                })
                .catch(error => {
                    console.info(error);
                    this.error = error.response.data.error ||  'URL is unvalid.';

                });
        }
    }
};
</script>
