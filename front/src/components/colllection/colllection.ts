import MiniGrid from 'minigrid'
import {computed, defineComponent, nextTick, onMounted, provide, ref, watch} from 'vue'

import ColllectElement from '@/src/components/element/Element.vue'
import useColllection from '@/src/composables/useColllection'
import useWindowStore from '@/src/stores/window'

export default defineComponent({
	name: 'ColllectColllection',
	components: {
		ColllectElement,
	},
	props: {
		encodedColllectionPath: {
			type: String as () => string,
			required: true,
		},
	},
	setup(props) {
		const ELEMENT_SELECTOR = '.c-colllect-element'

		const domContainer = ref<HTMLElement>()
		const grid = ref<MiniGrid>()
		const mustRecreateTheGrid = ref(false)
		const elementWidth = ref(130)

		const {
			name,
			isLoaded,
			elements,
			loadColllection,
		} = useColllection()

		const classes = computed(() => {
			return {
				'c-colllect-colllection__loaded': isLoaded.value,
			}
		})

		const gridUpdatesCount = ref(0)
		provide('gridUpdatesCount', gridUpdatesCount)
		const updateGrid = (): void => {
			if (domContainer.value === undefined) {
				return
			}

			if (!grid.value || mustRecreateTheGrid.value) {
				// Reset the flag
				mustRecreateTheGrid.value = false

				// Create a new grid
				grid.value = new MiniGrid({
					container: domContainer.value,
					item: domContainer.value?.querySelectorAll(ELEMENT_SELECTOR),
					gutter: 20,
				})
			}

			// (Re)compute the grid element positions
			grid.value.mount()

			gridUpdatesCount.value += 1
		}

		const updateColllectionElementWidth = () => {
			if (domContainer.value === undefined) {
				return
			}

			const firstElement = domContainer.value.querySelector(ELEMENT_SELECTOR)
			if (firstElement) {
				elementWidth.value = firstElement.getBoundingClientRect().width
			}
		}

		watch(
			elements,
			async () => {
				// Gives some time to the browser to compute new element width
				await nextTick()
				updateColllectionElementWidth()

				// Lets the time to elements to compute their min-height
				await nextTick()
				mustRecreateTheGrid.value = true
				updateGrid()
			}
		)

		const windowStore = useWindowStore()
		const watchableWindowWidth = computed<number>(() => {
			return windowStore.width
		})
		watch(
			watchableWindowWidth,
			async () => {
				updateColllectionElementWidth()
				await nextTick()
				updateGrid()
			}
		)

		onMounted(async () => {
			await nextTick()
			loadColllection(props.encodedColllectionPath)
		})

		return {
			domContainer,
			classes,
			name,
			isLoaded,
			elements,
			elementWidth,
			updateGrid,
		}
	},
})
