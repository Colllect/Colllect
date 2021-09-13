import Vue from 'vue'

import App from './App'
import router from './router'
import store from './store'

new Vue({
  store,
  router,
  render: h => h(App),
}).$mount('#app')

// Hack for dev only
if (window.location.hostname === 'localhost') {
  window.location.hostname = 'colllect.localhost'
}
