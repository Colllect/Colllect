import {Component, Prop, Vue} from 'vue-property-decorator'
import ColllectRadioGroup from '../RadioGroup'
import WithRender from './Radio.html'

@WithRender
@Component
export default class ColllectRadio extends Vue {
  /**
   * Props
   */
  @Prop()
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
  private radioGroupId: string = ''
  private focused: boolean = false

  /*
   * Computed
   */

  get groupValue(): string {
    return this.radioGroup.value
  }

  set groupValue(value: string) {
    this.$parent.$emit('input', value)
  }

  get isDisabled() {
    return this.disabled || this.radioGroup.disabled
  }

  get radioGroup(): ColllectRadioGroup {
    return this.$parent as ColllectRadioGroup
  }

  get classes() {
    return {
      'c-colllect-radio__disabled': this.isDisabled,
      'c-colllect-radio__focused': this.focused,
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
    this.radioGroupId = this.radioGroup.id
    this.id = 'c-colllect-radio--' + this.radioGroupId + '-' + (Math.random() + 1).toString(36).substring(2, 5)
  }
}
