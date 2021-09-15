import md5 from 'md5'
import {computed, defineComponent, nextTick, onMounted, ref, watch} from 'vue'

import {Element} from '@/src/api'
import ElementTypes from '@/src/models/ElementTypes'
import colllectionStore from '@/src/store/modules/colllection'
import windowStore from '@/src/store/modules/window'

const VERTICAL_DELTA = 200 // In pixels

export default defineComponent({
	name: 'ColllectElement',
	props: {
		element: {
			type: Object as () => Element,
			required: true,
		},
	},
	emits: [
		'load',
	],
	setup(props, {emit}) {
		const domElement = ref<HTMLElement>()
		const isLoaded = ref(false)
		const show = ref(false)
		const ratio = ref(1)

		const type = computed<string | undefined>(() => {
			return props.element.type
		})

		const name = computed<string | undefined>(() => {
			return props.element.name
		})

		const tags = computed<string[] | undefined>(() => {
			return props.element.tags
		})

		const updatedDate = computed<string | undefined>(() => {
			return props.element.updated
		})

		const size = computed<number | undefined>(() => {
			return props.element.size
		})

		const fileUrl = computed<string | undefined>(() => {
			return props.element.fileUrl
		})

		const isImage = computed<boolean>(() => {
			return type.value === ElementTypes.Image
		})

		const watchableWindowScrollAndHeight = computed<string>(() => {
			return [
				windowStore.state.scrollTop,
				windowStore.state.height,
			].join('|')
		})

		const classes = computed(() => {
			return {
				'c-colllect-element__loaded': isLoaded.value,
				'c-colllect-element__show': show.value,
				'c-colllect-element__type-colors': type.value === ElementTypes.Colors,
				'c-colllect-element__type-image': type.value === ElementTypes.Image,
				'c-colllect-element__type-link': type.value === ElementTypes.Link,
				'c-colllect-element__type-note': type.value === ElementTypes.Note,
			}
		})

		const style = computed(() => {
			return {
				minHeight: Math.ceil(colllectionStore.state.elementWidth * ratio.value) + 'px',
			}
		})

		const localStorageRatioKey = computed<string | undefined>(() => {
			if (fileUrl.value === undefined) {
				return
			}

			return 'elmtRatio.' + md5(fileUrl.value)
		})

		// @Throttle(300, {leading: true, trailing: true})
		const updateShow = (): void => {
			if (!domElement.value) {
				return
			}

			const elementClientRect = domElement.value.getBoundingClientRect()
			const elementTop = elementClientRect.top
			const elementBottom = elementTop + elementClientRect.height
			const windowHeight = window.innerHeight

			// Double the delta to show earlier if already loaded
			const delta = VERTICAL_DELTA * (isLoaded.value ? 2 : 1)

			const topLimit = -delta
			const bottomLimit = windowHeight + delta

			show.value = elementBottom > topLimit && elementTop < bottomLimit
		}

		/**
		 * Lets the browser recompute the layer in Colllection
		 * component before do heavy getBoundingClientRect computation
		 */
		const updateShowOnNextTick = async () => {
			await nextTick()
			updateShow()
		}

		const imageLoaded = async (e: Event) => {
			isLoaded.value = true

			if (e.currentTarget) {
				const {
					width,
					height,
				} = (e.currentTarget as HTMLElement).getBoundingClientRect()

				const clientRectRatio = parseFloat((height / width).toFixed(5))

				if (!clientRectRatio) {
					return
				}

				ratio.value = clientRectRatio

				if (localStorageRatioKey.value === undefined) {
					return
				}
				localStorage.setItem(localStorageRatioKey.value, ratio.value.toString())
			}

			// Used to call updateGrid on Colllection component
			await nextTick()
			emit('load')
		}

		watch(
			watchableWindowScrollAndHeight,
			(): void => {
				updateShowOnNextTick()
			}
		)

		onMounted((): void => {
			if (!isImage.value) {
				isLoaded.value = true
			}

			let cachedRatio = localStorageRatioKey.value !== undefined ? localStorage.getItem(localStorageRatioKey.value) : undefined
			if (!cachedRatio) {
				cachedRatio = '1'
			}

			ratio.value = parseFloat(cachedRatio)

			// FIXME
			domElement.value?.parentNode?.$on('updateGrid', () => {
				updateShowOnNextTick()
			})
		})

		return {
			domElement,
			classes,
			style,
			name,
			tags,
			updatedDate,
			size,
			isImage,
			show,
			isLoaded,
			fileUrl,
			imageLoaded,
		}
	},
})
