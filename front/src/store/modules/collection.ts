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
    Promise.all([
      api.getApiCollectionsByEncodedCollectionPath({encodedCollectionPath}),
      api.getApiCollectionsByEncodedCollectionPathElements({encodedCollectionPath}),
    ]).then(([collectionResponse, elementsResponse]) => {
      collection.commitSetCollection(collectionResponse.body)
      collection.commitSetElements(elementsResponse.body)
    })
  },
}

const collection = {
  get state() {
    return collectionModule.state()
  },

  commitSetCollection: collectionModule.commit(mutations.setCollection),
  commitSetElements: collectionModule.commit(mutations.setElements),

  dispatchLoadCollection: collectionModule.dispatch(actions.loadCollection),
}

export default collection
