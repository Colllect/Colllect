import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

let router = new VueRouter({
  mode: 'history',
  routes: [{
    name: 'home',
    path: '/',
    component: require('./components/home/Home.vue')
  }]
})

new Vue({
  el: '#app',
  components: {
  },
  router
})
