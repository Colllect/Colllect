import {computed, defineComponent, onMounted, ref} from 'vue'

import ColllectRadioGroup from '@/src/components/radio-group/RadioGroup.vue'

export default defineComponent({
	name: 'ColllectRadio',
	props: {
		modelValue: {
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
	},
	emits: [
		'update:modelValue',
	],
	setup(props) {
		const id = ref('')
		const radioGroupId = ref('')
		const focused = ref(false)

		const groupValue = computed<string>({
			get() {
				return radioGroup.value.value
			},
			set(newValue) {
				radioGroup.value.emit('update:modelValue', newValue)
			},
		})

		const isDisabled = computed(() => {
			return props.disabled || radioGroup.value.disabled
		})

		const radioGroup = computed(() => {
			return $parent.value as typeof ColllectRadioGroup
		})

		const classes = computed(() => {
			return {
				'c-colllect-radio__disabled': isDisabled.value,
				'c-colllect-radio__focused': focused.value,
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
			radioGroupId.value = radioGroup.value.id
			id.value = 'c-colllect-radio--' + radioGroupId.value + '-' + (Math.random() + 1).toString(36).substring(2, 5)
		})

		return {
			classes,
			groupValue,
			radioGroupId,
			isDisabled,
			focus,
			blur,
		}
	},
})
