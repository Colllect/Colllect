import {getStoreBuilder} from 'vuex-typex'

import {RootState} from '../state'

export interface WindowState {
  scrollTop: number,
  width: number,
  height: number,
}

const windowState: WindowState = {
  scrollTop: 0,
  width: 0,
  height: 0,
}

const windowModule = getStoreBuilder<RootState>().module('window', windowState)

const mutations = {
  setScrollTop: (state: WindowState, payload: number) => {
    state.scrollTop = payload
  },
  setWidth: (state: WindowState, payload: number) => {
    state.width = payload
  },
  setHeight: (state: WindowState, payload: number) => {
    state.height = payload
  },
}

const actions = {
  windowScroll: ({}, scrollTop: number) => {
    windowStore.commitSetScrollTop(scrollTop)
  },
  windowResize: ({}, payload: { width: number, height: number }) => {
    windowStore.commitSetWidth(payload.width)
    windowStore.commitSetHeight(payload.height)
  },
}

const stateGetter = windowModule.state()

const windowStore = {
  get state(): WindowState {
    return stateGetter()
  },

  commitSetScrollTop: windowModule.commit(mutations.setScrollTop),
  commitSetWidth: windowModule.commit(mutations.setWidth),
  commitSetHeight: windowModule.commit(mutations.setHeight),

  dispatchWindowScroll: windowModule.dispatch(actions.windowScroll),
  dispatchWindowResize: windowModule.dispatch(actions.windowResize),
}

export default windowStore
