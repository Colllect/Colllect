import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import ColllectButton from '../../components/button/Button'
import ColllectCheckbox from '../../components/checkbox/Checkbox'
import ColllectInput from '../../components/input/Input'
import ColllectRadio from '../../components/radio-group/radio/Radio'
import ColllectRadioGroup from '../../components/radio-group/RadioGroup'

@WithRender
@Component({
  components: {
    ColllectButton,
    ColllectCheckbox,
    ColllectInput,
    ColllectRadio,
    ColllectRadioGroup,
  },
})
export default class StyleguidePage extends Vue {
  private inputEmailValue: string = ''
  private inputPasswordValue: string = ''
  private checkboxRememberMeValue: boolean = false
  private radioValue: string = ''

  get isEmailErrored(): boolean {
    return this.inputEmailValue.length > 0 && !/\S+@\S+\.\S+/.test(this.inputEmailValue)
  }
}
