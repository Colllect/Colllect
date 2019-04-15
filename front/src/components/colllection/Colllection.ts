import MiniGrid from 'minigrid'
import {Component, Prop, Vue, Watch} from 'vue-property-decorator'
import WithRender from './Colllection.html'

import {Colllection, Element} from './../../api'

import colllectionStore from '../../store/modules/colllection'

import ColllectElement from '../element/Element'

@WithRender
@Component({
  components: {
    ColllectElement,
  },
})
export default class ColllectColllection extends Vue {
  @Prop({required: true})
  private encodedColllectionPath!: string

  private grid!: MiniGrid
  private updateGridHandler!: () => void

  get name(): string {
    return this.$store.state.colllection.name
  }

  get elements(): Element[] {
    return this.$store.state.colllection.elements
  }

  @Watch('elements')
  private onElementsChanged() {
    Vue.nextTick(() => {
      this.updateGrid(true)
    })
  }

  private updateGrid(mustRecreateTheGrid: boolean = false) {
    if (!this.grid || mustRecreateTheGrid) {
      this.grid = new MiniGrid({
        container: '.c-colllect-colllection--elements',
        item: '.c-colllect-element',
        gutter: 20,
      })
    }

    this.grid.mount()
  }

  private mounted() {
    Vue.nextTick(() => {
      colllectionStore.dispatchLoadColllection(this.encodedColllectionPath)
    })

    this.updateGridHandler = this.updateGrid.bind(this)
    window.addEventListener('resize', this.updateGridHandler)
  }

  private destroy() {
    window.removeEventListener('resize', this.updateGridHandler)
  }
}
