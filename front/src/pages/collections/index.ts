import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import {Collection} from '../../api'

import collectionsStore from '../../store/modules/collections'

@WithRender
@Component
export default class CollectionsPage extends Vue {
  get collections(): Collection[] {
    return this.$store.state.collections.collections
  }

  private mounted() {
    Vue.nextTick(() => {
      collectionsStore.dispatchLoadCollections()
    })
  }
}
