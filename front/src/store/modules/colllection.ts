import {getStoreBuilder} from 'vuex-typex'

import api, * as ApiInterfaces from '../../api'
import {RootState} from '../state'

export interface ColllectionState {
  name: string | null,
  encodedColllectionPath: string | null,
  elements: ApiInterfaces.Element[],
}

const colllectionState: ColllectionState = {
  name: null,
  encodedColllectionPath: null,
  elements: [],
}

const colllectionModule = getStoreBuilder<RootState>().module('colllection', colllectionState)

const mutations = {
  setName: (state: ColllectionState, payload: string|null) => {
    state.name = payload
  },
  setColllection: (state: ColllectionState, payload: ApiInterfaces.Colllection) => {
    state.name = payload.name
    state.encodedColllectionPath = payload.encodedColllectionPath
  },
  setElements: (state: ColllectionState, payload: ApiInterfaces.Element[]) => {
    state.elements = payload
  },
}

const actions = {
  loadColllection: ({}, encodedColllectionPath: string) => {
    colllectionStore.commitSetName(null)
    colllectionStore.commitSetElements([])

    api.getApiColllectionsByEncodedColllectionPath({encodedColllectionPath}).then((colllectionResponse) => {
      colllectionStore.commitSetColllection(colllectionResponse.body)
    })
    api.getApiColllectionsByEncodedColllectionPathElements({encodedColllectionPath}).then((elementsResponse) => {
      colllectionStore.commitSetElements(elementsResponse.body.itemListElement)
    })
  },
}

const colllectionStore = {
  get state() {
    return colllectionModule.state()
  },

  commitSetName: colllectionModule.commit(mutations.setName),
  commitSetColllection: colllectionModule.commit(mutations.setColllection),
  commitSetElements: colllectionModule.commit(mutations.setElements),

  dispatchLoadColllection: colllectionModule.dispatch(actions.loadColllection),
}

export default colllectionStore
