import '@mdi/font/css/materialdesignicons.css'
import './assets/fonts.css'
import { createApp } from 'vue'
import { router } from './router'
import { vuetify } from './vuetify'
import App from './App.vue'

const app = createApp(App)
app.use(router)
app.use(vuetify)
app.mount('#app')
