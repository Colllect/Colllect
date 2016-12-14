import Vue from 'vue'
import VueRouter from 'vue-router'

import CollectHeader from './components/header/Header'

require('../assets/images/sprite/logo.svg')

Vue.use(VueRouter)

let router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/',
      redirect: { name: 'inbox' }
    },
    {
      name: 'inbox',
      path: '/inbox',
      component: require('./pages/inbox/Inbox.vue')
    },
    {
      name: 'collections',
      path: '/collections',
      component: require('./pages/collections/Collections.vue')
    }
  ]
})

new Vue({
  el: '#app',
  components: {
    CollectHeader
  },
  router
})
