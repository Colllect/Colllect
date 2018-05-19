import Vue from 'vue'
import VueRouter from 'vue-router'

import Collection from './pages/collection'
import Collections from './pages/collections'
import Inbox from './pages/inbox'
import Login from './pages/login'
import Register from './pages/register'

const routes = [
  {path: '/', redirect: {name: 'inbox'}},
  {name: 'login', path: '/login', component: Login},
  {name: 'register', path: '/register', component: Register},
  {name: 'inbox', path: '/inbox', component: Inbox},
  {name: 'collections', path: '/collections', component: Collections},
  {name: 'collection', path: '/collections/:encodedCollectionPath', component: Collection},
]

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes,
})

export default router
