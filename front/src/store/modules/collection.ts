import {getStoreBuilder} from 'vuex-typex'

import api, * as ApiInterfaces from '../../api'
import {RootState} from '../state'

export interface CollectionState {
  name: string | null,
  encodedCollectionPath: string | null,
  elements: ApiInterfaces.Element[],
}

const collectionState: CollectionState = {
  name: null,
  encodedCollectionPath: null,
  elements: [],
}

const collectionModule = getStoreBuilder<RootState>().module('collection', collectionState)

const mutations = {
  setName: (state: CollectionState, payload: string|null) => {
    state.name = payload
  },
  setCollection: (state: CollectionState, payload: ApiInterfaces.Collection) => {
    state.name = payload.name
    state.encodedCollectionPath = payload.encoded_collection_path
  },
  setElements: (state: CollectionState, payload: ApiInterfaces.Element[]) => {
    state.elements = payload
  },
}

const actions = {
  loadCollection: ({}, encodedCollectionPath: string) => {
    collectionStore.commitSetName(null)
    collectionStore.commitSetElements([])

    api.getApiCollectionsByEncodedCollectionPath({encodedCollectionPath}).then((collectionResponse) => {
      collectionStore.commitSetCollection(collectionResponse.body)
    })
    api.getApiCollectionsByEncodedCollectionPathElements({encodedCollectionPath}).then((elementsResponse) => {
      collectionStore.commitSetElements(elementsResponse.body)
    })
  },
}

const collectionStore = {
  get state() {
    return collectionModule.state()
  },

  commitSetName: collectionModule.commit(mutations.setName),
  commitSetCollection: collectionModule.commit(mutations.setCollection),
  commitSetElements: collectionModule.commit(mutations.setElements),

  dispatchLoadCollection: collectionModule.dispatch(actions.loadCollection),
}

export default collectionStore
