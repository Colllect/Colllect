import Vue from 'vue'
import VueRouter from 'vue-router'

import Colllection from './pages/colllection'
import Colllections from './pages/colllections'
import Inbox from './pages/inbox'
import Styleguide from './pages/styleguide'

const routes = [
  {path: '/', redirect: {name: 'inbox'}},
  {name: 'inbox', path: '/inbox', component: Inbox},
  {name: 'colllections', path: '/colllections', component: Colllections},
  {name: 'colllection', path: '/colllections/:encodedColllectionPath', component: Colllection},
  {name: 'styleguide', path: '/styleguide', component: Styleguide},
]

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes,
})

export default router
