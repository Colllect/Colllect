import {getStoreBuilder} from 'vuex-typex'

import api, * as ApiInterfaces from '@/src/api'
import {RootState} from '@/src/store/state'

export interface ColllectionsState {
  colllections: ApiInterfaces.Colllection[]
}

const colllectionsState: ColllectionsState = {
  colllections: [],
}

const colllectionsModule = getStoreBuilder<RootState>().module('colllections', colllectionsState)

const mutations = {
  setColllections: (state: ColllectionsState, payload: ApiInterfaces.Colllection[]) => {
    state.colllections = payload
  },
}

const actions = {
  loadColllections: () => {
    Promise.all([
      api.getApiColllections({}),
    ]).then(([colllectionsResponse]) => {
      colllectionsStore.commitSetColllections(colllectionsResponse.body)
    })
  },
}

const stateGetter = colllectionsModule.state()

const colllectionsStore = {
  get state(): ColllectionsState {
    return stateGetter()
  },

  commitSetColllections: colllectionsModule.commit(mutations.setColllections),

  dispatchLoadColllections: colllectionsModule.dispatch(actions.loadColllections),
}

export default colllectionsStore
