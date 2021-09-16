import {acceptHMRUpdate, defineStore} from 'pinia'

import api, * as ApiInterfaces from '@/src/api'
import base64UriDecode from '@/src/functions/base64Uri'

export interface ColllectionState {
  name?: string
  encodedColllectionPath?: string
  elements: ApiInterfaces.Element[]
  elementWidth: number
  isLoaded: boolean
}

const useColllectionStore = defineStore({
	id: 'colllection',
	state: (): ColllectionState => {
		return {
			name: undefined,
			encodedColllectionPath: undefined,
			elements: [],
			elementWidth: 130,
			isLoaded: false,
		}
	},
	actions: {
		async loadColllection(encodedColllectionPath: string) {
			const name = base64UriDecode(encodedColllectionPath).split('/').pop()
			this.name = name
			this.elements = []
			this.isLoaded = false

			const loadColllectionPromise = api
				.getApiColllectionsByEncodedColllectionPath({encodedColllectionPath})
				.then((colllectionResponse) => {
					if (colllectionResponse.status !== 200) {
						return
					}

					this.name = colllectionResponse.body.name
					this.encodedColllectionPath = colllectionResponse.body.encodedColllectionPath
				})

			const loadColllectionElementsPromise = api
				.getApiColllectionsByEncodedColllectionPathElements({encodedColllectionPath})
				.then((elementsResponse) => {
					if (elementsResponse.status !== 200) {
						return
					}

					this.elements = elementsResponse.body
				})

			await Promise.all([
				loadColllectionPromise,
				loadColllectionElementsPromise,
			])
			this.isLoaded = true
		},
	},
})

export default useColllectionStore

if (import.meta.hot) {
	import.meta.hot.accept(acceptHMRUpdate(useColllectionStore, import.meta.hot))
}
