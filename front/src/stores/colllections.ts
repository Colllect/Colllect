import {acceptHMRUpdate, defineStore} from 'pinia'

import ColllectionService from '@/src/services/colllectionService'
import {Colllection} from '@/src/types/api/definitions'

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
			ColllectionService.getColllections()
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
