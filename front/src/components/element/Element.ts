import {Throttle} from 'lodash-decorators'
import * as md5 from 'md5'
import {Component, Prop, Vue, Watch} from 'vue-property-decorator'
import ElementTypes from '../../models/ElementTypes'
import WithRender from './Element.html'

import {Element} from './../../api'

import colllectionStore from '../../store/modules/colllection'
import windowStore from '../../store/modules/window'

@WithRender
@Component
export default class ColllectElement extends Vue {
  private static readonly VERTICAL_DELTA = 200 // In pixels

  @Prop({required: true})
  private element!: Element

  private isLoaded: boolean = false
  private show: boolean = false
  private ratio: number = 1

  get type(): string | undefined {
    return this.element.type
  }

  get name(): string | undefined {
    return this.element.name
  }

  get tags(): string[] | undefined {
    return this.element.tags
  }

  get updatedDate(): string | undefined {
    return this.element.updated
  }

  get size(): number | undefined {
    return this.element.size
  }

  get fileUrl(): string | undefined {
    return this.element.fileUrl
  }

  get isImage(): boolean {
    return this.type === ElementTypes.Image
  }

  get watchableWindowScrollAndHeight(): string {
    return [
      windowStore.state.scrollTop,
      windowStore.state.height,
    ].join('|')
  }

  get classes(): object {
    return {
      'c-colllect-element__loaded': this.isLoaded,
      'c-colllect-element__show': this.show,
      'c-colllect-element__type-colors': this.type === ElementTypes.Colors,
      'c-colllect-element__type-image': this.type === ElementTypes.Image,
      'c-colllect-element__type-link': this.type === ElementTypes.Link,
      'c-colllect-element__type-note': this.type === ElementTypes.Note,
    }
  }

  get style(): object {
    return {
      minHeight: Math.ceil(colllectionStore.state.elementWidth * this.ratio) + 'px',
    }
  }

  get localStorageRatioKey(): string | undefined {
    if (this.fileUrl === undefined) {
      return
    }

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

    // Double the delta to show earlier if already loaded
    const DELTA = ColllectElement.VERTICAL_DELTA * (this.isLoaded ? 2 : 1)

    const topLimit = - DELTA
    const bottomLimit = windowHeight + DELTA

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

      if (this.localStorageRatioKey === undefined) {
        return
      }
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
    if (!this.isImage) {
      this.isLoaded = true
    }

    let ratio = this.localStorageRatioKey !== undefined ? localStorage.getItem(this.localStorageRatioKey) : undefined
    if (!ratio) {
      ratio = '1'
    }

    this.ratio = parseFloat(ratio)

    this.$parent.$on('updateGrid', () => {
      this.updateShowOnNextTick()
    })
  }
}
