import { createApp } from 'vue'
import Header from './layouts/Header.vue'
import router from './router/index'
import { Quasar, Notify, Dialog  } from 'quasar'
import 'quasar/dist/quasar.css';
import '@quasar/extras/material-icons/material-icons.css';
const app = createApp(Header)

app.use(Quasar, {
    plugins: {
      Notify,
      Dialog
    },
  })
app.use(router)

app.mount('#app')
