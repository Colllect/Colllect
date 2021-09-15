import {createRouter, createWebHistory} from 'vue-router'

import Colllection from '@/src/pages/colllection/ColllectionPage.vue'
import Colllections from '@/src/pages/colllections/ColllectionsPage.vue'
import Inbox from '@/src/pages/inbox/InboxPage.vue'
import Styleguide from '@/src/pages/styleguide/StyleguidePage.vue'

const routes = [
  {path: '/', redirect: {name: 'inbox'}},
  {name: 'inbox', path: '/inbox', component: Inbox},
  {name: 'colllections', path: '/colllections', component: Colllections},
  {name: 'colllection', path: '/colllections/:encodedColllectionPath', component: Colllection},
  {name: 'styleguide', path: '/styleguide', component: Styleguide},
]

const router = createRouter({
	history: createWebHistory(),
  routes,
})

export default router
