import {throttle} from 'lodash-es'
import md5 from 'md5'
import {computed, defineComponent, nextTick, onMounted, ref, watch} from 'vue'

import {Element} from '@/src/api'
import ElementTypes from '@/src/models/ElementTypes'
import useWindowStore from '@/src/stores/window'

const VERTICAL_DELTA = 200 // In pixels

export default defineComponent({
	name: 'ColllectElement',
	props: {
		element: {
			type: Object as () => Element,
			required: true,
		},
		elementWidth: {
			type: Number as () => number,
			required: true,
		},
	},
	emits: [
		'load',
	],
	setup(props, {emit}) {
		const windowStore = useWindowStore()

		const domElement = ref<HTMLElement>()
		const isLoaded = ref(false)
		const show = ref(false)
		const ratio = ref(1)

		const isImage = computed<boolean>(() => {
			return props.element.type === ElementTypes.Image
		})

		const classes = computed(() => {
			return {
				'c-colllect-element__loaded': isLoaded.value,
				'c-colllect-element__show': show.value,
				'c-colllect-element__type-colors': props.element.type === ElementTypes.Colors,
				'c-colllect-element__type-image': props.element.type === ElementTypes.Image,
				'c-colllect-element__type-link': props.element.type === ElementTypes.Link,
				'c-colllect-element__type-note': props.element.type === ElementTypes.Note,
			}
		})

		const style = computed(() => {
			return {
				minHeight: Math.ceil(props.elementWidth * ratio.value) + 'px',
			}
		})

		const localStorageRatioKey = computed<string | undefined>(() => {
			const fileUrl = props.element.fileUrl
			if (fileUrl === undefined) {
				return
			}

			return 'elmtRatio.' + md5(fileUrl)
		})

		const updateShow = throttle((): void => {
			if (domElement.value === undefined) {
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
		}, 300, {
			leading: true,
			trailing: true,
		})

		/**
		 * Lets the browser recompute the layer in Colllection
		 * component before do heavy getBoundingClientRect computation
		 */
		const updateShowOnNextTick = async () => {
			await nextTick()
			updateShow()
		}

		const onImageLoaded = async (e: Event) => {
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

		const watchableWindow = computed<string>(() => {
			return [
				windowStore.scrollTop,
				windowStore.width,
				windowStore.height,
			].join('|')
		})
		watch(
			watchableWindow,
			(): void => {
				updateShowOnNextTick()
			},
			{
				immediate: true,
			},
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
		})

		return {
			domElement,
			classes,
			style,
			isImage,
			show,
			isLoaded,
			onImageLoaded,
		}
	},
})
