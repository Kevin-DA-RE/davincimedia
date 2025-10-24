import { createApp } from 'vue'
import Layout from './layouts/Layout.vue'
import router from './router/index'
import { Quasar, Notify, Dialog, Loading  } from 'quasar'
import 'quasar/dist/quasar.css';
import '@quasar/extras/material-icons/material-icons.css';

const app = createApp(Layout)

app.use(Quasar, {
    plugins: {
      Notify,
      Dialog,
      Loading
    },
  })
app.use(router)

app.mount('#app')
