import {acceptHMRUpdate, defineStore} from 'pinia'

import api, * as ApiInterfaces from '@/src/api'

export interface ColllectionsState {
  colllections: ApiInterfaces.Colllection[]
}

const useColllectionsStore = defineStore({
	id: 'colllections',
	state: (): ColllectionsState => {
		return {
			colllections: [],
		}
	},
	actions: {
		loadColllections() {
			api.getApiColllections({})
				.then((colllectionsResponse) => {
					if (colllectionsResponse.status !== 200) {
						return
					}

					this.colllections = colllectionsResponse.body
				})
		},
	},
})

export default useColllectionsStore

if (import.meta.hot) {
	import.meta.hot.accept(acceptHMRUpdate(useColllectionsStore, import.meta.hot))
}
