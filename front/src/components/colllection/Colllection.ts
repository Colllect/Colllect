import {Throttle} from 'lodash-decorators'
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
  private mustRecreateTheGrid: boolean = false

  get name(): string {
    return this.$store.state.colllection.name
  }

  get elements(): Element[] {
    return this.$store.state.colllection.elements
  }

  get watchableWindowWidth(): number {
    return this.$store.state.window.width
  }

  private updateGrid(): void {
    if (!this.grid || this.mustRecreateTheGrid) {
      // Reset the flag
      this.mustRecreateTheGrid = false

      // Create a new grid
      this.grid = new MiniGrid({
        container: '.c-colllect-colllection--elements',
        item: '.c-colllect-element',
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
