import MiniGrid from 'minigrid'
import {computed, defineComponent, nextTick, onMounted, ref, watch} from 'vue'

import {Element} from '@/src/api'
import ColllectElement from '@/src/components/element/Element.vue'
import useColllectionStore from '@/src/stores/colllection'
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
	emits: [
		'updateGrid',
	],
	setup(props, {emit}) {
		const colllectionStore = useColllectionStore()
		const windowStore = useWindowStore()

		const container = ref<HTMLElement>()
		const grid = ref<MiniGrid>()
		const mustRecreateTheGrid = ref(false)

		const name = computed<string | undefined>(() => {
			return colllectionStore.name
		})

		const isLoaded = computed<boolean>(() => {
			return colllectionStore.isLoaded
		})

		const elements = computed<Element[]>(() => {
			return colllectionStore.elements
		})

		const watchableWindowWidth = computed<number>(() => {
			return windowStore.width
		})

		const classes = computed(() => {
			return {
				'c-colllect-colllection__loaded': isLoaded.value,
			}
		})

		const updateGrid = (): void => {
			if (container.value === undefined) {
				return
			}

			if (!grid.value || mustRecreateTheGrid.value) {
				// Reset the flag
				mustRecreateTheGrid.value = false

				// Create a new grid
				grid.value = new MiniGrid({
					container: container.value,
					item: container.value.childNodes,
					gutter: 20,
				})
			}

			// (Re)compute the grid element positions
			grid.value.mount()

			emit('updateGrid')
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

		watch(
			watchableWindowWidth,
			async () => {
				updateColllectionElementWidth()
				await nextTick()
				updateGrid()
			}
		)

		const updateColllectionElementWidth = () => {
			if (container.value === undefined) {
				return
			}

			const firstElement = container.value.querySelector('.c-colllect-element')
			if (firstElement) {
				colllectionStore.elementWidth = firstElement.getBoundingClientRect().width
			}
		}

		onMounted(async () => {
			await nextTick()
			colllectionStore.loadColllection(props.encodedColllectionPath)
		})

		return {
			classes,
			name,
			isLoaded,
			elements,
			updateGrid,
		}
	},
})
