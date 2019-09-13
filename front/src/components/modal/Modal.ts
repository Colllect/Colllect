import {Component, Model, Prop, Vue} from 'vue-property-decorator'
import WithRender from './Modal.html'

import A11yFocusTrap from '../a11y-focus-trap/A11yFocusTrap'

@WithRender
@Component({
  components: {
    A11yFocusTrap,
  },
})
export default class ColllectModal extends Vue {
  /*
   * Props
   */

  @Prop()
  private width!: string

  @Prop()
  private height!: string

  /*
   * Methods
   */

  private close(e?: KeyboardEvent): void {
    if (e && e.type === 'keydown') {
      if (e.key !== 'Escape') {
        return
      }

      e.stopPropagation()
    }

    const focusTrap = this.$refs.focusTrap as A11yFocusTrap
    focusTrap.close()

    this.$emit('close')
  }

  /*
   * Hooks
   */

  private created(): void {
    // Listen for escape keydown event to close the popup
    window.addEventListener('keydown', this.close, false)
  }
  private mounted(): void {
    const focusTrap = this.$refs.focusTrap as A11yFocusTrap
    focusTrap.open()
  }
  private beforeDestroy(): void {
    window.removeEventListener('keydown', this.close, false)
  }
}
