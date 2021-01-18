import MiniGrid from 'minigrid'
import {Component, Prop, Vue, Watch} from 'vue-property-decorator'
import WithRender from './Colllection.html'

import {Element} from './../../api'

import colllectionStore from '../../store/modules/colllection'
import windowStore from '../../store/modules/window'

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
  private mustRecreateTheGrid: boolean = false

  get name(): string | undefined {
    return colllectionStore.state.name
  }

  get isLoaded(): boolean {
    return colllectionStore.state.isLoaded
  }

  get elements(): Element[] {
    return colllectionStore.state.elements
  }

  get watchableWindowWidth(): number {
    return windowStore.state.width
  }

  get classes(): object {
    return {
      'c-colllect-colllection__loaded': this.isLoaded,
    }
  }

  private updateGrid(): void {
    if (!this.grid || this.mustRecreateTheGrid) {
      // Reset the flag
      this.mustRecreateTheGrid = false

      // Create a new grid
      const container = this.$refs.container as Node
      this.grid = new MiniGrid({
        container,
        item: container.childNodes,
        gutter: 20,
      })
    }

    // (Re)compute the grid element positions
    this.grid.mount()

    this.$emit('updateGrid')
  }

  @Watch('elements')
  private onElementsChanged(): void {
    // Gives some time to the browser to compute new element width
    Vue.nextTick(() => {
      this.updateColllectionElementWidth()

      // Lets the time to elements to compute their min-height
      Vue.nextTick(() => {
        this.mustRecreateTheGrid = true
        this.updateGrid()
      })
    })
  }

  @Watch('watchableWindowWidth')
  private onWindowResizeWidth(): void {
    this.updateColllectionElementWidth()
    Vue.nextTick(() => {
      this.updateGrid()
    })
  }

  private updateColllectionElementWidth() {
    if (this.$el) {
      const firstElement = this.$el.querySelector('.c-colllect-element')
      if (firstElement) {
        colllectionStore.commitSetElementWidth(firstElement.getBoundingClientRect().width)
      }
    }
  }

  private mounted() {
    Vue.nextTick(() => {
      colllectionStore.dispatchLoadColllection(this.encodedColllectionPath)
    })
  }
}
