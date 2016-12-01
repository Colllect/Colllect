import Vue from 'vue'
import VueRouter from 'vue-router'

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
    }
  ]
})

new Vue({
  el: '#app',
  components: {},
  router
})
