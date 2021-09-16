import {defineComponent, onBeforeUnmount, onMounted, ref} from 'vue'

import A11yFocusTrap from '@/src/components/a11y-focus-trap/A11yFocusTrap.vue'

export default defineComponent({
	name: 'ColllectModal',
	components: {
		A11yFocusTrap,
	},
	props: {
		show: {
			type: Boolean as () => boolean,
			required: true,
		},
		width: {
			type: String as () => string,
		},
		height: {
			type: String as () => string,
		},
	},
	emits: [
		'close',
	],
	setup(_props, {emit}) {
		const focusTrap = ref<HTMLElement>()

		const close = (e?: KeyboardEvent): void => {
			if (e && e.type === 'keydown') {
				if (e.key !== 'Escape') {
					return
				}

				e.stopPropagation()
			}

			focusTrap.value?.close()

			emit('close')
		}

		onMounted(() => {
			// Listen for escape keydown event to close the popup
			window.addEventListener('keydown', close, false)
			focusTrap.value?.open()
		})

		onBeforeUnmount(() => {
			window.removeEventListener('keydown', close, false)
		})

		return {
			close,
		}
	},
})
