import debounce from 'lodash-es/debounce'
import throttle from 'lodash-es/throttle'
import {Component, Vue} from 'vue-property-decorator'
import WithRender from './App.html'

import ColllectAddElement from './components/add-element/AddElement'
import ColllectHeader from './components/header/Header'

import authStore from './store/modules/auth'
import windowStore from './store/modules/window'

@WithRender
@Component({
  components: {
    ColllectAddElement,
    ColllectHeader,
  },
})
export default class App extends Vue {
  /*
  * Data
  */

  private showAddElementModal: boolean = true

  /*
   * Computed
   */

  private get isAuthenticated(): boolean {
    return authStore.isAuthenticated
  }

  private get scrollableNode(): HTMLElement {
    return document.querySelector('.m-app--main') as HTMLElement
  }

  /*
   * Methods
   */

  private handleScroll() {
    windowStore.dispatchWindowScroll(this.scrollableNode.scrollTop)
  }

  private handleResize() {
    windowStore.dispatchWindowResize({
      width: window.innerWidth,
      height: window.innerHeight,
    })
  }

  /*
   * Hooks
   */

  private mounted() {
    authStore.dispatchGetCurrentUser()

    this.scrollableNode.addEventListener('scroll', throttle(this.handleScroll, 300, {leading: false}))
    window.addEventListener('resize', debounce(this.handleResize, 300, {leading: true}))
  }
}
