import {Component, Vue} from 'vue-property-decorator'
import WithRender from './index.html'

import user from '../../store/modules/user'

@WithRender
@Component
export default class Register extends Vue {
  private email: string = ''
  private password: string = ''
  private nickname: string = ''
  private error: string|null = null

  private get isAuthenticated() {
    return this.$store.state.auth.nickname != null
  }

  private register() {
    user.dispatchRegister({
      email: this.email,
      password: this.password,
      nickname: this.nickname,
    }).then(() => {
      this.email = ''
      this.password = ''
      this.nickname = ''
      this.error = null
    }).catch((err) => {
      if (err.body) {
        this.error = err.body.message
      }
    })
  }
}
