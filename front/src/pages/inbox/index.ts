import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import ColllectCollection from '../../components/collection/Collection'

import {Collection} from '../../api'

import collection from '../../store/modules/collection'

@WithRender
@Component({
  components: {
    ColllectCollection,
  },
})
export default class Inbox extends Vue {
  private static collectionName: string = 'Inbox'
  private static encodedCollectionPath: string = encodeURIComponent(btoa(Inbox.collectionName))

  private get collection(): Collection {
    return {
      name: Inbox.collectionName,
      encoded_collection_path: Inbox.encodedCollectionPath,
    }
  }
}
