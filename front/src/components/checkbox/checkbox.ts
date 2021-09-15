import {computed, defineComponent, onMounted, ref} from 'vue'

export default defineComponent({
	name: 'ColllectCheckbox',
	props: {
		checked: {
			type: Boolean as () => boolean,
			default: false,
		},
		disabled: {
			type: Boolean as () => boolean,
		},
		errored: {
			type: Boolean as () => boolean,
			default: false,
		},
		errorMessage: {
			type: String as () => string,
		},
	},
	emits: [
		'change',
	],
	setup(props, {emit}) {
		const id = ref('')
		const focused = ref(false)

		const checked = computed<boolean>({
			get() {
				return props.checked
			},
			set(value) {
				emit('change', value)
			},
		})

		const classes = computed(() => {
			return {
				'c-colllect-checkbox__disabled': props.disabled,
				'c-colllect-checkbox__focused': focused.value,
				'c-colllect-checkbox__errored': props.errored,
			}
		})

		const focus = (): void => {
			focused.value = true
		}

		const blur = (): void => {
			focused.value = false
			setTimeout(() => {
				if (document.activeElement instanceof HTMLElement) {
					document.activeElement.blur()
				}
			})
		}

		onMounted((): void => {
			id.value = 'c-colllect-checkbox--' + (Math.random() + 1).toString(36).substring(2, 5)
		})

		return {
			checked,
			classes,
			focus,
			blur,
		}
	},
})
