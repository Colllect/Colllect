import {getStoreBuilder} from 'vuex-typex'

import api, * as ApiInterfaces from '../../api'
import {RootState} from '../state'

const userModule = getStoreBuilder<RootState>().module('user', {})

const actions = {
  register: async ({}, form: { email: string, password: string, nickname: string }) => {
    const response = await api.postApiUsers({
      email: form.email,
      plainPassword: form.password,
      nickname: form.nickname,
    })

    const body: ApiInterfaces.User = response.body
  },
}

const userStore = {
  get state() {
    return userModule.state()
  },

  dispatchRegister: userModule.dispatch(actions.register),
}

export default userStore
