import Vue from 'vue'

import App from './App'
import router from './router'
import store from './store'

const CollectApp = new Vue({
  el: '#app',
  components: {App},
  render(h) {
    return h('App')
  },
  router,
  store,
})

// Hack for dev only
if (window.location.hostname === 'localhost') {
  window.location.hostname = 'colllect.localhost'
}
