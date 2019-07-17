import {Component, Model, Prop, Vue} from 'vue-property-decorator'
import WithRender from './Select.html'

@WithRender
@Component
export default class ColllectSelect extends Vue {
  /**
   * Props
   */

  @Prop()
  private label!: string

  @Prop()
  @Model('change', {type: String})
  private value!: string

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
  private wasOpenByMouse: boolean = false

  /*
   * Computed
   */

  get localValue(): string {
    return this.value
  }

  set localValue(value: string) {
    this.$emit('change', value)

    // Manage the visual focus state when selecting a value
    if (this.wasOpenByMouse && document.activeElement instanceof HTMLElement) {
      document.activeElement.blur()
    }
    this.wasOpenByMouse = false
  }

  get classes() {
    return {
      'c-colllect-select__disabled': this.disabled,
      'c-colllect-select__focused': this.focused,
      'c-colllect-select__errored': this.errored,
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

    // Reset on blur
    this.wasOpenByMouse = false
  }

  private mouseDown(): void {
    this.wasOpenByMouse = true
  }

  /*
   * Hooks
   */
  private mounted(): void {
    this.id = 'c-colllect-select--' + (Math.random() + 1).toString(36).substring(2, 5)
  }
}
