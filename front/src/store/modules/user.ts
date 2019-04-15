import {getStoreBuilder} from 'vuex-typex'

import {RootState} from '../state'

const userModule = getStoreBuilder<RootState>().module('user', {})

const userStore = {
  get state() {
    return userModule.state()
  },
}

export default userStore
