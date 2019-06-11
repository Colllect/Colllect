import {Throttle} from 'lodash-decorators'
import * as md5 from 'md5'
import {Component, Prop, Vue, Watch} from 'vue-property-decorator'
import WithRender from './Element.html'

import {Element} from './../../api'

@WithRender
@Component
export default class ColllectElement extends Vue {
  private static readonly TYPES = {
    COLORS: 'colors',
    IMAGE: 'image',
    LINK: 'link',
    NOTE: 'note',
  }
  private static readonly VERTICAL_DELTA = 200 // In pixels

  @Prop({required: true})
  private element!: Element

  private isLoaded: boolean = false
  private show: boolean = false
  private ratio: number = 1

  get type(): string {
    return this.element.type
  }

  get name(): string {
    return this.element.name
  }

  get tags(): string[] {
    return this.element.tags
  }

  get updatedDate(): string {
    return this.element.updated
  }

  get size(): number {
    return this.element.size
  }

  get fileUrl(): string {
    return this.element.fileUrl
  }

  get watchableWindowScrollAndHeight(): string {
    return [
      this.$store.state.window.scrollTop,
      this.$store.state.window.height,
    ].join('|')
  }

  get classes(): object {
    return {
      'c-colllect-element__loaded': this.isLoaded,
      'c-colllect-element__show': this.show,
      'c-colllect-element__type-colors': this.type === ColllectElement.TYPES.COLORS,
      'c-colllect-element__type-image': this.type === ColllectElement.TYPES.IMAGE,
      'c-colllect-element__type-link': this.type === ColllectElement.TYPES.LINK,
      'c-colllect-element__type-note': this.type === ColllectElement.TYPES.NOTE,
    }
  }

  get style(): object {
    return {
      minHeight: Math.ceil(this.$store.state.colllection.elementWidth * this.ratio) + 'px',
    }
  }

  get localStorageRatioKey(): string {
    return 'elmtRatio.' + md5(this.fileUrl)
  }

  @Throttle(300, {leading: true, trailing: true})
  private updateShow(): void {
    if (!this.$el) {
      return
    }

    const elementClientRect = this.$el.getBoundingClientRect()
    const elementTop = elementClientRect.top
    const elementBottom = elementTop + elementClientRect.height
    const windowHeight = window.innerHeight

    const topLimit = - ColllectElement.VERTICAL_DELTA
    const bottomLimit = windowHeight + ColllectElement.VERTICAL_DELTA

    this.show = elementBottom > topLimit && elementTop < bottomLimit
  }

  /**
   * Lets the browser recompute the layer in Colllection
   * component before do heavy getBoundingClientRect computation
   */
  private updateShowOnNextTick(): void {
    Vue.nextTick(() => {
      this.updateShow()
    })
  }

  private imageLoaded(e: Event): void {
    this.isLoaded = true

    if (e.currentTarget) {
      const {
        width,
        height,
      } = (e.currentTarget as HTMLElement).getBoundingClientRect()

      const ratio = parseFloat((height / width).toFixed(5))

      if (!ratio) {
        return
      }

      this.ratio = ratio
      localStorage.setItem(this.localStorageRatioKey, this.ratio.toString())
    }

    // Used to call updateGrid on Colllection component
    Vue.nextTick(() => {
      this.$emit('load')
    })
  }

  @Watch('watchableWindowScrollAndHeight')
  private onWindowScrollOrHeight(): void {
    this.updateShowOnNextTick()
  }

  private mounted(): void {
    let ratio = localStorage.getItem(this.localStorageRatioKey)
    if (!ratio) {
      ratio = '1'
    }

    this.ratio = parseFloat(ratio)

    this.$parent.$on('updateGrid', () => {
      this.updateShowOnNextTick()
    })
  }
}
