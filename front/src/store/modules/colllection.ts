import {getStoreBuilder} from 'vuex-typex'

import api, * as ApiInterfaces from '../../api'
import {base64UriDecode} from '../../helpers/base64Uri'
import {RootState} from '../state'

export interface ColllectionState {
  name: string | null,
  encodedColllectionPath: string | null,
  elements: ApiInterfaces.Element[],
  elementWidth: number,
  isLoaded: boolean,
}

const colllectionState: ColllectionState = {
  name: null,
  encodedColllectionPath: null,
  elements: [],
  elementWidth: 130,
  isLoaded: false,
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
  setElementWidth: (state: ColllectionState, payload: number) => {
    state.elementWidth = payload
  },
  setIsLoaded: (state: ColllectionState, payload: boolean) => {
    state.isLoaded = payload
  },
}

const actions = {
  loadColllection: ({}, encodedColllectionPath: string) => {
    const name = base64UriDecode(encodedColllectionPath).split('/').pop() || null
    colllectionStore.commitSetName(name)
    colllectionStore.commitSetElements([])
    colllectionStore.commitSetIsLoaded(false)

    const loadColllectionPromise = api
      .getApiColllectionsByEncodedColllectionPath({encodedColllectionPath})
      .then((colllectionResponse) => {
        colllectionStore.commitSetColllection(colllectionResponse.body)
      })

    const loadColllectionElementsPromise = api
      .getApiColllectionsByEncodedColllectionPathElements({encodedColllectionPath})
      .then((elementsResponse) => {
        colllectionStore.commitSetElements(elementsResponse.body.itemListElement)
      })

    Promise.all([loadColllectionPromise, loadColllectionElementsPromise]).then(() => {
      colllectionStore.commitSetIsLoaded(true)
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
  commitSetElementWidth: colllectionModule.commit(mutations.setElementWidth),
  commitSetIsLoaded: colllectionModule.commit(mutations.setIsLoaded),

  dispatchLoadColllection: colllectionModule.dispatch(actions.loadColllection),
}

export default colllectionStore
