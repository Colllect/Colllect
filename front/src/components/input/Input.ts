import {Component, Prop, Vue} from 'vue-property-decorator'
import WithRender from './Input.html'

@WithRender
@Component
export default class ColllectInput extends Vue {
  private static TYPES = {
    TEXT: 'text',
    EMAIL: 'email',
    PASSWORD: 'password',
  }
  private static AUTOCOMPLETE = {
    OFF: 'off',
    NEW_PASSWORD: 'new-password',
  }

  /**
   * Props
   */

  @Prop({
      default: ColllectInput.TYPES.TEXT,
      validator(value: string): boolean {
        return [ColllectInput.TYPES.TEXT, ColllectInput.TYPES.EMAIL, ColllectInput.TYPES.PASSWORD].includes(value)
      },
    },
  )
  private type!: string

  @Prop({default: ''})
  private value!: string

  @Prop({default: ''})
  private placeholder!: string

  @Prop({default: false})
  private errored!: boolean

  @Prop()
  private errorMessage?: string

  @Prop({default: false})
  private autofocus!: boolean

  @Prop({
      default: ColllectInput.AUTOCOMPLETE.OFF,
      validator(value: string): boolean {
        return [ColllectInput.AUTOCOMPLETE.OFF, ColllectInput.AUTOCOMPLETE.NEW_PASSWORD].includes(value)
      },
    },
  )
  private autocomplete!: string

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

  get localValue(): string {
    return this.value
  }

  set localValue(value: string) {
    this.$emit('input', value)
  }

  get classes() {
    return {
      'c-colllect-input__disabled': this.disabled,
      'c-colllect-input__focused': this.focused,
      'c-colllect-input__errored': this.errored,
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
  }

  /*
   * Hooks
   */
  private mounted(): void {
    this.id = 'c-colllect-input--' + (Math.random() + 1).toString(36).substring(2, 5)
  }
}
