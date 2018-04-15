import * as Cookies from 'tiny-cookie'
import {getStoreBuilder} from 'vuex-typex'

import api, * as ApiInterfaces from '../../api'
import {RootState} from '../state'

const jwtCookieKey = 'access_token'
const jwtCookieExpires = '1Y'

export interface AuthState {
  id: number|null,
  nickname: string|null,
  roles: string[],
}

const authState: AuthState = {
  id: null,
  nickname: null,
  roles: [],
}

const authModule = getStoreBuilder<RootState>().module('auth', authState)

const parseJwt = (jwt: string): AuthState => JSON.parse(atob(jwt.split('.')[1]))

const mutations = {
  setUser: (state: AuthState, payload: AuthState) => {
    state.id = payload.id
    state.nickname = payload.nickname
    state.roles = payload.roles
  },
  resetUser: (state: AuthState) => {
    state.id = 0
    state.nickname = ''
    state.roles = []
  },
}

const actions = {
  login: async ({}, form: { email: string, password: string, stayLoggedIn: boolean }) => {
    const response = await api.postApiTokens({
      email: form.email,
      password: form.password,
    })

    const body: ApiInterfaces.Token = response.body
    const jwt = body.token

    if (form.stayLoggedIn) {
      Cookies.set(jwtCookieKey, jwt, encodeURIComponent, {expires: jwtCookieExpires})
    }

    authStore.commitSetUser(parseJwt(jwt))
  },
  tryLoginFromCookie: () => {
    authStore.jwt = Cookies.get(jwtCookieKey) as string

    if (authStore.jwt != null && authStore.jwt.length > 0) {
      authStore.commitSetUser(parseJwt(authStore.jwt))
    }
  },
  logout: () => {
    Cookies.remove(jwtCookieKey)
    authStore.commitResetUser()
    authStore.jwt = ''
  },
}

const authStore = {
  jwt: '',
  get state() {
    return authModule.state()
  },

  commitSetUser: authModule.commit(mutations.setUser),
  commitResetUser: authModule.commit(mutations.resetUser),

  dispatchLogin: authModule.dispatch(actions.login),
  dispatchTryLoginFromCookie: authModule.dispatch(actions.tryLoginFromCookie),
  dispatchLogout: authModule.dispatch(actions.logout),
}

export default authStore
