import Vue from 'vue'
import VueRouter from 'vue-router'

import App from './App'
import AppRouter from './AppRouter'

Vue.use(VueRouter)

const CollectApp = new Vue({
  el: '#app',
  components: {App},
  render(h) {
    return h('App')
  },
  router: AppRouter,
})
