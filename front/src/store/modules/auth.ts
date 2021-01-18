import {getStoreBuilder} from 'vuex-typex'

import api from '../../api'
import {RootState} from '../state'

export interface AuthState {
  id: number | null,
  nickname: string | null,
  roles: string[],
}

const authState: AuthState = {
  id: null,
  nickname: null,
  roles: [],
}

const authModule = getStoreBuilder<RootState>().module('auth', authState)

const getters = {
  isAuthenticated: authModule.read(function isAuthenticated(state): boolean {
    return state.nickname !== null
  }),
}

const mutations = {
  setUser: (state: AuthState, payload: AuthState) => {
    state.id = payload.id
    state.nickname = payload.nickname
    state.roles = payload.roles
  },
  resetUser: (state: AuthState) => {
    state.id = null
    state.nickname = null
    state.roles = []
  },
}

const actions = {
  getCurrentUser: () => {
    api.getApiUsersCurrent({})
      .then((currentUserResponse) => {
        if (currentUserResponse.status !== 200) {
          return
        }

        authStore.commitSetUser({
          id: currentUserResponse.body.id as number,
          nickname: currentUserResponse.body.nickname,
          roles: currentUserResponse.body.roles as string[],
        })
      })
  },
}

const stateGetter = authModule.state()

const authStore = {
  get state(): AuthState {
    return stateGetter()
  },
  get isAuthenticated(): boolean {
    return getters.isAuthenticated()
  },

  commitSetUser: authModule.commit(mutations.setUser),
  commitResetUser: authModule.commit(mutations.resetUser),

  dispatchGetCurrentUser: authModule.dispatch(actions.getCurrentUser),
}

export default authStore
