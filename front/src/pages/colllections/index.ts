import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import {Colllection} from '../../api'

import colllectionsStore from '../../store/modules/colllections'

@WithRender
@Component
export default class ColllectionsPage extends Vue {
  get colllections(): Colllection[] {
    return colllectionsStore.state.colllections
  }

  private mounted() {
    Vue.nextTick(() => {
      colllectionsStore.dispatchLoadColllections()
    })
  }
}
