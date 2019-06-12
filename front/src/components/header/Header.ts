import {Component, Vue} from 'vue-property-decorator'
import WithRender from './Header.html'

import authStore from '../../store/modules/auth'

@WithRender
@Component
export default class ColllectHeader extends Vue {
  get nickname() {
    return authStore.state.nickname
  }

  get isAuthenticated() {
    return authStore.isAuthenticated
  }
}
