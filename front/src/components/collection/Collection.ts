import MiniGrid from 'minigrid'
import {Component, Prop, Vue, Watch} from 'vue-property-decorator'
import WithRender from './Collection.html'

import {Collection, Element} from './../../api'

import collectionStore from '../../store/modules/collection'

import ColllectElement from '../element/Element'

@WithRender
@Component({
  components: {
    ColllectElement,
  },
})
export default class ColllectCollection extends Vue {
  @Prop({required: true})
  private encodedCollectionPath!: string

  private grid!: MiniGrid
  private updateGridHandler!: () => void

  get name(): string {
    return this.$store.state.collection.name
  }

  get elements(): Element[] {
    return this.$store.state.collection.elements
  }

  @Watch('elements')
  private onElementsChanged(value: Element[], oldValue: Element[]) {
    Vue.nextTick(() => {
      this.updateGrid(true)
    })
  }

  private updateGrid(mustRecreateTheGrid: boolean = false) {
    if (!this.grid || mustRecreateTheGrid) {
      this.grid = new MiniGrid({
        container: '.c-colllect-collection--elements',
        item: '.c-colllect-element',
        gutter: 20,
      })
    }

    this.grid.mount()
  }

  private mounted() {
    Vue.nextTick(() => {
      collectionStore.dispatchLoadCollection(this.encodedCollectionPath)
    })

    this.updateGridHandler = this.updateGrid.bind(this)
    window.addEventListener('resize', this.updateGridHandler)
  }

  private destroy() {
    window.removeEventListener('resize', this.updateGridHandler)
  }
}
