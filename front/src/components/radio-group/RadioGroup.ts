import {Component, Prop, Vue} from 'vue-property-decorator'
import WithRender from './RadioGroup.html'

@WithRender
@Component
export default class ColllectRadioGroup extends Vue {
  /*
   * Data
   */

  public id: string = ''

  /**
   * Props
   */

  @Prop()
  public value!: string

  @Prop({default: false})
  public disabled!: boolean

  @Prop({default: false})
  private errored!: boolean

  @Prop()
  private errorMessage?: string

  /*
   * Computed
   */

  get localValue(): string {
    return this.value
  }

  set localValue(value: string) {
    this.$emit('change', value)
  }

  get classes() {
    return {
      'c-colllect-checkbox__errored': this.errored,
    }
  }

  /*
   * Hooks
   */

  private mounted(): void {
    this.id = 'c-colllect-radio-group--' + (Math.random() + 1).toString(36).substring(2, 5)
  }
}
