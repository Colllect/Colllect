import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import auth from '../../store/modules/auth'

@WithRender
@Component
export default class Login extends Vue {
  private email: string = ''
  private password: string = ''
  private stayLoggedIn: boolean = true
  private error: string|null = null

  private get isAuthenticated() {
    return this.$store.state.auth.nickname != null
  }

  private login() {
    auth.dispatchLogin({
      email: this.email,
      password: this.password,
      stayLoggedIn: this.stayLoggedIn,
    }).then(() => {
      this.error = null
    }).catch((err) => {
      if (err.body) {
        this.error = err.body.message
      }
    })
  }

  private logout() {
    auth.dispatchLogout()
  }
}
