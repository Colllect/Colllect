import {computed, defineComponent, onMounted, ref} from 'vue'

export default defineComponent({
	name: 'ColllectRadioGroup',
	props: {
		modelValue: {
			type: String as () => string,
			required: true,
		},
		disabled: {
			type: Boolean as () => boolean,
			default: false,
		},
		errored: {
			type: Boolean as () => boolean,
			default: false,
		},
		errorMessage: {
			type: String as () => string,
		},
	},
	setup(props, {emit}) {
		const id = ref('')

		const localValue = computed<string>({
			get() {
				return props.modelValue
			},
			set(value) {
				emit('change', value)
			},
		})

		onMounted((): void => {
			id.value = 'c-colllect-radio-group--' + (Math.random() + 1).toString(36).substring(2, 5)
		})

		return {
			id,
			localValue,
		}
	},
})
