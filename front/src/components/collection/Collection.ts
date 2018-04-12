import MiniGrid from 'minigrid'
import {Component, Prop, Vue, Watch} from 'vue-property-decorator'
import WithRender from './Collection.html'

import {Collection, Element} from './../../api'

import collection from '../../store/modules/collection'

import ColllectElement from '../element/Element'

@WithRender
@Component({
  components: {
    ColllectElement,
  },
})
export default class ColllectCollection extends Vue {
  @Prop({required: true})
  private collection!: Collection

  private grid!: MiniGrid
  private updateGridHandler!: () => void

  get elements(): Element[] {
    return this.$store.state.collection.elements
  }

  @Watch('elements')
  private onElementsChanged(value: Element[], oldValue: Element[]) {
    Vue.nextTick(() => {
      this.grid = new MiniGrid({
        container: '.c-colllect-collection--elements',
        item: '.c-colllect-element',
        gutter: 20,
      })
      this.grid.mount()

      setTimeout(() => { this.grid.mount() })
    })
  }

  private updateGrid() {
    if (this.grid) {
      this.grid.mount()
    }
  }

  private mounted() {
    Vue.nextTick(() => {
      collection.dispatchLoadCollection(this.collection.encoded_collection_path)
    })

    this.updateGridHandler = this.updateGrid.bind(this)
    window.addEventListener('resize', this.updateGridHandler)
  }

  private destroy() {
    window.removeEventListener('resize', this.updateGridHandler)
  }
}
