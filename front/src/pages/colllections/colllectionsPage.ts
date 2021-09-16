import {computed, defineComponent, nextTick, onMounted} from 'vue'

import {Colllection} from '@/src/api'
import useColllectionsStore from '@/src/stores/colllections'

export default defineComponent({
	name: 'ColllectionsPage',
	setup() {
		const colllectionsStore = useColllectionsStore()

		const colllections = computed<Colllection[]>(() => {
			return colllectionsStore.colllections
		})

		onMounted(async () => {
			await nextTick()
			await colllectionsStore.loadColllections()
		})

		return {
			colllections,
		}
	},
})
