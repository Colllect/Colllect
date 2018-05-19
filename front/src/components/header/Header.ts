import {Component, Vue} from 'vue-property-decorator'
import WithRender from './Header.html'

import auth from '../../store/modules/auth'

@WithRender
@Component
export default class ColllectHeader extends Vue {
  private get nickname() {
    return this.$store.state.auth.nickname
  }

  private get isAuthenticated() {
    return this.$store.state.auth.nickname != null
  }

  private logout() {
    auth.dispatchLogout()
  }
}
