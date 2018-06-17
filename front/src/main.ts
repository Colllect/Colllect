import Vue from 'vue'

import App from './App'
import router from './router'
import store from './store'

import auth from './store/modules/auth'

const CollectApp = new Vue({
  el: '#app',
  components: {App},
  render(h) {
    return h('App')
  },
  router,
  store,
  mounted() {
    auth.dispatchTryLoginFromCookie()
  },
})

// Hack for dev only
if (window.location.hostname === 'localhost') {
  window.location.hostname = 'colllect.localhost'
}
