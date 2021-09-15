import {computed, defineComponent, nextTick, onMounted} from 'vue'

import {Colllection} from '@/src/api'
import colllectionsStore from '@/src/store/modules/colllections'

export default defineComponent({
	name: 'ColllectionsPage',
	setup() {
		const colllections = computed<Colllection[]>(() => {
			return colllectionsStore.state.colllections
		})

		onMounted(async () => {
			await nextTick()
			await colllectionsStore.dispatchLoadColllections()
		})

		return {
			colllections,
		}
	},
})
