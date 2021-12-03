import { computed, defineComponent, nextTick, onMounted } from 'vue'

import useColllectionsStore from '@/src/stores/colllections'
import { Colllection } from '@/src/types/api/definitions'

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
