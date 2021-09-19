import {acceptHMRUpdate, defineStore} from 'pinia'

import {Colllection, ColllectionsService} from '@/src/api'

export interface ColllectionsState {
  colllections: Colllection[]
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
			ColllectionsService.getColllections()
				.then((colllections) => {
					this.colllections = colllections
				})
		},
	},
})

export default useColllectionsStore

if (import.meta.hot) {
	import.meta.hot.accept(acceptHMRUpdate(useColllectionsStore, import.meta.hot))
}
