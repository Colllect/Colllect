import { computed, defineComponent, onMounted, ref } from 'vue'

enum InputTypes {
	Text = 'text',
	Email = 'email',
	Password = 'password',
}

enum InputAutocomplete {
	Off = 'off',
	NewPassword = 'new-password',
}

export default defineComponent({
	name: 'ColllectInput',
	props: {
		type: {
			type: String as () => InputTypes,
			default: InputTypes.Text,
			validator: (value: InputTypes) => {
				return [InputTypes.Text,
					InputTypes.Email,
					InputTypes.Password].includes(value)
			},
		},
		modelValue: {
			type: String as () => string,
			required: true,
		},
		placeholder: {
			type: String as () => string,
		},
		errored: {
			type: Boolean as () => boolean,
		},
		errorMessage: {
			type: String as () => string,
		},
		disabled: {
			type: Boolean as () => boolean,
		},
		autofocus: {
			type: Boolean as () => boolean,
		},
		autocomplete: {
			type: String as () => InputAutocomplete,
			default: InputAutocomplete.Off,
			validator: (value: InputAutocomplete) => {
				return [InputAutocomplete.Off,
					InputAutocomplete.NewPassword].includes(value)
			},
		},
	},
	emits: [
		'update:modelValue',
	],
	setup(props, { emit }) {
		const input = ref<HTMLInputElement>()
		const id = ref('')
		const focused = ref(false)

		const localValue = computed<string>({
			get() {
				return props.modelValue
			},
			set(newValue) {
				emit('update:modelValue', newValue)
			},
		})

		const classes = computed(() => {
			return {
				'c-colllect-input__disabled': props.disabled,
				'c-colllect-input__focused': focused.value,
				'c-colllect-input__errored': props.errored,
				'c-colllect-input__autocomplete-off': props.autocomplete === InputAutocomplete.Off,
			}
		})

		const focus = (): void => {
			focused.value = true
		}

		const blur = (): void => {
			focused.value = false
		}

		onMounted((): void => {
			id.value = 'c-colllect-input--' + (Math.random() + 1).toString(36).substring(2, 5)
			if (props.autofocus) {
				input.value?.focus()
			}
		})

		return {
			input,
			id,
			focused,
			localValue,
			classes,
			focus,
			blur,
		}
	},
})
