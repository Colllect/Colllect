import {Component, Model, Prop, Vue} from 'vue-property-decorator'
import WithRender from './Checkbox.html'

@WithRender
@Component
export default class ColllectCheckbox extends Vue {
  /**
   * Props
   */
  @Prop({default: false})
  @Model('change', {type: Boolean})
  private checked!: boolean

  @Prop({default: false})
  private errored!: boolean

  @Prop()
  private errorMessage?: string

  @Prop({default: false})
  private disabled!: boolean

  /*
   * Data
   */

  private id: string = ''
  private focused: boolean = false

  /*
   * Computed
   */

  get localChecked(): boolean {
    return this.checked
  }

  set localChecked(value: boolean) {
    this.$emit('change', value)
  }

  get classes() {
    return {
      'c-colllect-checkbox__disabled': this.disabled,
      'c-colllect-checkbox__focused': this.focused,
      'c-colllect-checkbox__errored': this.errored,
    }
  }

  /*
   * Methods
   */

  private focus(): void {
    this.focused = true
  }

  private blur(): void {
    this.focused = false
    setTimeout(() => {
      if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur()
      }
    })
  }

  /*
   * Hooks
   */
  private mounted(): void {
    this.id = 'c-colllect-checkbox--' + (Math.random() + 1).toString(36).substring(2, 5)
  }
}
