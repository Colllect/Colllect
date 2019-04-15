import {Component, Prop, Vue} from 'vue-property-decorator'
import WithRender from './Element.html'

import {Element} from './../../api'

@WithRender
@Component
export default class ColllectElement extends Vue {
  @Prop({required: true})
  private element!: Element

  private isLoaded: boolean = false
  private showImage: boolean = false
  private imageRatio: number = 1

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

  get proxyUrl(): string {
    return this.element.proxyUrl
  }

  get classes(): object {
    return {
      'c-colllect-element__loaded': this.isLoaded,
    }
  }

  get style(): object {
    let width = 150
    if (this.$el) {
      width = this.$el.getBoundingClientRect().width
    }

    return {
      minHeight: width * this.imageRatio + 'px',
    }
  }

  get localStorageRatioKey(): string {
    return 'element.ratio.' + encodeURI(btoa(this.proxyUrl))
  }

  private updateShowImage(): void {
    if (!this.$el) {
      return
    }

    const elementClientRect = this.$el.getBoundingClientRect()
    const elementTop = elementClientRect.top
    const elementBottom = elementTop + elementClientRect.height

    if (!document.documentElement) {
      return
    }

    const viewportTop = document.documentElement.scrollTop
    const viewportBottom = viewportTop + window.innerHeight

    this.showImage = elementBottom > viewportTop && elementTop < viewportBottom
  }

  private imageLoaded(e: Event): void {
    this.isLoaded = true

    if (e.currentTarget) {
      const {
        width,
        height,
      } = (e.currentTarget as HTMLElement).getBoundingClientRect()
      localStorage.setItem(this.localStorageRatioKey, (height / width).toString())
    }

    Vue.nextTick(() => {
      this.$emit('load')
    })
  }

  private mounted(): void {
    let ratio = localStorage.getItem(this.localStorageRatioKey)
    if (!ratio) {
      ratio = '1'
    }

    this.imageRatio = parseFloat(ratio)

    Vue.nextTick(() => {
      this.updateShowImage()
    })
  }
}
