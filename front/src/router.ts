import Vue from 'vue'
import VueRouter from 'vue-router'

import Colllection from './pages/colllection'
import Colllections from './pages/colllections'
import Inbox from './pages/inbox'
import Login from './pages/login'
import Register from './pages/register'

const routes = [
  {path: '/', redirect: {name: 'inbox'}},
  {name: 'login', path: '/login', component: Login},
  {name: 'register', path: '/register', component: Register},
  {name: 'inbox', path: '/inbox', component: Inbox},
  {name: 'colllections', path: '/colllections', component: Colllections},
  {name: 'colllection', path: '/colllections/:encodedColllectionPath', component: Colllection},
]

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes,
})

export default router
