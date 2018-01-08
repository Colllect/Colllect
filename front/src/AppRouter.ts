import VueRouter from 'vue-router'

import Collections from './pages/collections/Collections'
import Inbox from './pages/inbox/Inbox'

const routes = [
  {path: '/', redirect: {name: 'inbox'}},
  {name: 'inbox', path: '/inbox', component: Inbox},
  {name: 'collections', path: '/collections', component: Collections},
]

const router = new VueRouter({
  mode: 'history',
  routes,
})

export default router
