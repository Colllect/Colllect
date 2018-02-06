import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import collection from '../../store/modules/collection'

const INBOX_ENCODED_COLLECTION_PATH = encodeURIComponent(btoa('Inbox'))

@WithRender
@Component
export default class Inbox extends Vue {
  get encodedCollectionPath() {
    return INBOX_ENCODED_COLLECTION_PATH
  }

  get elements() {
    return this.$store.state.collection.elements
  }

  private mounted() {
    Vue.nextTick(() => {
      collection.dispatchLoadCollection(INBOX_ENCODED_COLLECTION_PATH)
    })
  }
}
