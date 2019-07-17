import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import ColllectButton from '../../components/button/Button'
import ColllectCheckbox from '../../components/checkbox/Checkbox'
import ColllectInput from '../../components/input/Input'
import ColllectRadio from '../../components/radio-group/radio/Radio'
import ColllectRadioGroup from '../../components/radio-group/RadioGroup'
import ColllectSelect from '../../components/select/Select'

@WithRender
@Component({
  components: {
    ColllectButton,
    ColllectCheckbox,
    ColllectInput,
    ColllectRadio,
    ColllectRadioGroup,
    ColllectSelect,
  },
})
export default class StyleguidePage extends Vue {
  private inputEmailValue: string = ''
  private inputPasswordValue: string = ''
  private checkboxRememberMeValue: boolean = false
  private radioValue: string = ''
  private selectValue: string = ''

  private readonly colorLists = [
    {
      '#769bf7': '$cornflower-blue',
      '#7b80de': '$chetwode-blue',
      '#565da3': '$scampi',
      '#313a68': '$rhino',
    },
    {
      '#b49edc': '$cold-purple',
    },
    {
      '#ff3d94': '$wild-strawberry',
      '#f72b86': '$violet-red',
      '#d23636': '$persian-red',
    },
    {
      '#ffffff': '$white',
      '#f3f7fa': '$catskill-white',
      '#92a4b1': '$gull-gray',
      '#8598a5': '$regent-gray',
      '#31383c': '$outer-space',
      '#1d1f21': '$shark',
    },
  ]

  private readonly fonts = {
    '16px "Cocogoose"': '$font-cocogoose',
    '600 16px "Source Sans Pro"': '$font-source-sans-pro',
    '400 16px "Source Sans Pro"': '$font-source-sans-pro',
  }

  get isEmailErrored(): boolean {
    return this.inputEmailValue.length > 0 && !/\S+@\S+\.\S+/.test(this.inputEmailValue)
  }
}
