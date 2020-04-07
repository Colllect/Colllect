import {getStoreBuilder} from 'vuex-typex'
import {Colllection} from '../../api'

import api, * as ApiInterfaces from '../../api'
import {base64UriDecode} from '../../helpers/base64Uri'
import {RootState} from '../state'

export interface ColllectionState {
  name?: string,
  encodedColllectionPath?: string,
  elements: ApiInterfaces.Element[],
  elementWidth: number,
  isLoaded: boolean,
}

const colllectionState: ColllectionState = {
  name: undefined,
  encodedColllectionPath: undefined,
  elements: [],
  elementWidth: 130,
  isLoaded: false,
}

const colllectionModule = getStoreBuilder<RootState>().module('colllection', colllectionState)

const mutations = {
  setName: (state: ColllectionState, payload?: string) => {
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
    const name = base64UriDecode(encodedColllectionPath).split('/').pop()
    colllectionStore.commitSetName(name)
    colllectionStore.commitSetElements([])
    colllectionStore.commitSetIsLoaded(false)

    const loadColllectionPromise = api
      .getApiColllectionsByEncodedColllectionPath({encodedColllectionPath})
      .then((colllectionResponse) => {
        colllectionStore.commitSetColllection(colllectionResponse.body as Colllection)
      })

    const loadColllectionElementsPromise = api
      .getApiColllectionsByEncodedColllectionPathElements({encodedColllectionPath})
      .then((elementsResponse) => {
        colllectionStore.commitSetElements(elementsResponse.body)
      })

    Promise.all([loadColllectionPromise, loadColllectionElementsPromise]).then(() => {
      colllectionStore.commitSetIsLoaded(true)
    })
  },
}

const stateGetter = colllectionModule.state()

const colllectionStore = {
  get state(): ColllectionState {
    return stateGetter()
  },

  commitSetName: colllectionModule.commit(mutations.setName),
  commitSetColllection: colllectionModule.commit(mutations.setColllection),
  commitSetElements: colllectionModule.commit(mutations.setElements),
  commitSetElementWidth: colllectionModule.commit(mutations.setElementWidth),
  commitSetIsLoaded: colllectionModule.commit(mutations.setIsLoaded),

  dispatchLoadColllection: colllectionModule.dispatch(actions.loadColllection),
}

export default colllectionStore
