import {getStoreBuilder} from 'vuex-typex'

import api, * as ApiInterfaces from '../../api'
import {RootState} from '../state'

export interface CollectionsState {
  collections: ApiInterfaces.Collection[],
}

const collectionsState: CollectionsState = {
  collections: [],
}

const collectionsModule = getStoreBuilder<RootState>().module('collections', collectionsState)

const mutations = {
  setCollections: (state: CollectionsState, payload: ApiInterfaces.Collection[]) => {
    state.collections = payload
  },
}

const actions = {
  loadCollections: () => {
    Promise.all([
      api.getApiCollections({}),
    ]).then(([collectionsResponse]) => {
      collectionsStore.commitSetCollections(collectionsResponse.body)
    })
  },
}

const collectionsStore = {
  get state() {
    return collectionsModule.state()
  },

  commitSetCollections: collectionsModule.commit(mutations.setCollections),

  dispatchLoadCollections: collectionsModule.dispatch(actions.loadCollections),
}

export default collectionsStore
