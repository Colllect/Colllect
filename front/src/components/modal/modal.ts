import {defineComponent, onBeforeUnmount, onMounted} from 'vue'

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
		const close = (e?: KeyboardEvent): void => {
			if (e && e.type === 'keydown') {
				if (e.key !== 'Escape') {
					return
				}

				e.stopPropagation()
			}

			emit('close')
		}

		onMounted(() => {
			// Listen for escape keydown event to close the popup
			window.addEventListener('keydown', close, false)
		})

		onBeforeUnmount(() => {
			window.removeEventListener('keydown', close, false)
		})

		return {
			close,
		}
	},
})
