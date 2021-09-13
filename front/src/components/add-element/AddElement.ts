import {Component, Prop, Vue} from 'vue-property-decorator'
import {Tag} from '../../../generated/api'
import ElementTypes from '../../models/ElementTypes'
import WithRender from './AddElement.html'

import ColllectButton from '../button/Button'
import ColllectInput from '../input/Input'
import ColllectModal from '../modal/Modal'

interface EnhancedTag extends Tag {
  color?: string
}

@WithRender
@Component({
  components: {
    ColllectButton,
    ColllectInput,
    ColllectModal,
  },
})
export default class ColllectAddElement extends Vue {
  /*
   * Props
   */

  @Prop({default: false})
  private show!: boolean

  /*
   * Data
   */

  private elementTypes: string[] = Object.values(ElementTypes)
  private currentElementType: string = ElementTypes.Image
  private url: string = ''
  private name: string = ''
  private tags: EnhancedTag[] = [
    // {name: 'Landing', encodedName: ''},
    // {name: 'Red', encodedName: ''},
    // {name: 'UI', encodedName: ''},
    // {name: 'Typography', encodedName: ''},
    // {name: 'User experience', encodedName: ''},
  ]
  private suggestedTags: EnhancedTag[] = [
    // {name: 'Shapes', encodedName: ''},
    // {name: 'Purple', encodedName: ''},
    // {name: 'Round', encodedName: ''},
  ]

  /*
   * Methods
   */
  private hideAddElementModal(): void {
    // TODO: add AddElement modal state to store and switch it to false
    this.$emit('close')
  }
}
