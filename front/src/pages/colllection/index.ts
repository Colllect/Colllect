import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import ColllectColllection from '../../components/colllection/Colllection'

@WithRender
@Component({
  components: {
    ColllectColllection,
  },
})
export default class ColllectionPage extends Vue {
  private get encodedColllectionPath(): string {
    return this.$route.params.encodedColllectionPath
  }
}
