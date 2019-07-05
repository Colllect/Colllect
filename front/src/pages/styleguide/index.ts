import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import ColllectButton from '../../components/button/Button'
import ColllectCheckbox from '../../components/checkbox/Checkbox'
import ColllectInput from '../../components/input/Input'

@WithRender
@Component({
  components: {
    ColllectButton,
    ColllectCheckbox,
    ColllectInput,
  },
})
export default class StyleguidePage extends Vue {
  private inputEmailValue: string = ''
  private inputPasswordValue: string = ''
  private checkboxRememberMeValue: boolean = false

  get isEmailErrored(): boolean {
    return this.inputEmailValue.length > 0 && !/\S+@\S+\.\S+/.test(this.inputEmailValue)
  }
}
