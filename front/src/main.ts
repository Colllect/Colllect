import '@/src/assets/scss/main.scss'

import {createApp} from 'vue'

import App from '@/src/App.vue'
import router from '@/src/router'
import stores from '@/src/stores'

const app = createApp(App)
app.use(router)
app.use(stores)
app.mount('#app')

// Hack for dev only
if (window.location.hostname === 'localhost') {
  window.location.hostname = 'colllect.localhost'
}
