import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import ColllectCollection from '../../components/collection/Collection'

@WithRender
@Component({
  components: {
    ColllectCollection,
  },
})
export default class CollectionPage extends Vue {
  private get encodedCollectionPath(): string {
    return this.$route.params.encodedCollectionPath
  }
}
