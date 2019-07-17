import {Component, Prop, Vue} from 'vue-property-decorator'
import WithRender from './Button.html'

@WithRender
@Component
export default class ColllectButton extends Vue {
  /**
   * Props
   */

  @Prop({default: false})
  private disabled!: boolean

  /*
   * Computed
   */

  get classes() {
    return {
      'c-colllect-button__disabled': this.disabled,
    }
  }

  /*
   * Methods
   */
  private onClick(event: Event): void {
    this.$emit('click', event)
  }

  private blur(): void {
    if (document.activeElement instanceof HTMLElement) {
      document.activeElement.blur()
    }
  }
}
