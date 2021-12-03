import { computed, defineComponent, onMounted, ref } from 'vue'

export default defineComponent({
	name: 'ColllectSelect',
	props: {
		label: {
			type: String as () => string,
		},
		modelValue: {
			type: String as () => string,
			required: true,
		},
		errored: {
			type: Boolean as () => boolean,
			default: false,
		},
		errorMessage: {
			type: String as () => string,
		},
		disabled: {
			type: Boolean as () => boolean,
			default: false,
		},
	},
	emits: [
		'update:modelValue',
	],
	setup(props, { emit }) {
		const id = ref('')
		const focused = ref(false)
		const wasOpenByMouse = ref(false)

		const localValue = computed<string>({
			get() {
				return props.modelValue
			},
			set(newValue) {
				emit('update:modelValue', newValue)

				// Manage the visual focus state when selecting a value
				if (wasOpenByMouse.value && document.activeElement instanceof HTMLElement) {
					document.activeElement.blur()
				}
				wasOpenByMouse.value = false
			},
		})

		const classes = computed(() => {
			return {
				'c-colllect-select__disabled': props.disabled,
				'c-colllect-select__focused': focused.value,
				'c-colllect-select__errored': props.errored,
			}
		})

		const focus = (): void => {
			focused.value = true
		}

		const blur = (): void => {
			focused.value = false

			// Reset on blur
			wasOpenByMouse.value = false
		}

		const mouseDown = (): void => {
			wasOpenByMouse.value = true
		}

		onMounted((): void => {
			id.value = 'c-colllect-select--' + (Math.random() + 1).toString(36).substring(2, 5)
		})

		return {
			id,
			classes,
			localValue,
			focus,
			blur,
			mouseDown,
		}
	},
})
