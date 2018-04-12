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
    return this.element.proxy_url
  }

  get classes(): object {
    return {
      'c-colllect-element__loaded': this.isLoaded,
    }
  }

  get style(): object {
    if (!this.showImage) {
      return {}
    }

    return {
      minHeight: this.$el.getBoundingClientRect().width * this.imageRatio + 'px',
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

    const viewportTop = document.documentElement.scrollTop
    const viewportBottom = viewportTop + window.innerHeight

    this.showImage = elementBottom > viewportTop && elementTop < viewportBottom
  }

  get imageRatio(): number {
    if (this.showImage) {
      let ratio = localStorage.getItem(this.localStorageRatioKey)
      if (!ratio) {
        ratio = '1'
      }
      return parseFloat(ratio)
    }

    return 1
  }

  private imageLoaded(e: Event): void {
    this.isLoaded = true

    if (e.srcElement) {
      const {
        width,
        height,
      } = e.srcElement.getBoundingClientRect()
      localStorage.setItem(this.localStorageRatioKey, (height / width).toString())
    }

    Vue.nextTick(() => {
      this.$emit('load')
    })
  }

  private mounted(): void {
    Vue.nextTick(() => {
      this.updateShowImage()
    })
  }
}
