import {Component, Prop, Vue} from 'vue-property-decorator'
import WithRender from './Button.html'

enum ButtonType {
  Default = 'default',
  Light = 'light',
}

@WithRender
@Component
export default class ColllectButton extends Vue {
  /**
   * Props
   */

  @Prop({default: false})
  private disabled!: boolean

  @Prop({default: ButtonType.Default})
  private type!: ButtonType

  /*
   * Computed
   */

  get classes() {
    return {
      'c-colllect-button__disabled': this.disabled,
      ['c-colllect-button__type-' + this.type]: this.type !== ButtonType.Default,
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
