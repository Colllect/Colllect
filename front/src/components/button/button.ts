import { computed, defineComponent } from 'vue'

enum ButtonType {
  Default = 'default',
  Light = 'light',
}

export default defineComponent({
	name: 'ColllectButton',
	props: {
		disabled: {
			type: Boolean as () => boolean,
			default: false,
		},
		type: {
			type: String as () => ButtonType,
			default: ButtonType.Default,
		},
	},
	emits: [
		'click',
	],
	setup(props, { emit }) {
		const classes = computed(() => {
			return {
				'c-colllect-button__disabled': props.disabled,
				['c-colllect-button__type-' + props.type]: props.type !== ButtonType.Default,
			}
		})

		const onClick = (event: Event): void => {
			emit('click', event)
		}

		const blur = (): void => {
			if (document.activeElement instanceof HTMLElement) {
				document.activeElement.blur()
			}
		}

		return {
			type: props.type,
			disabled: props.disabled,
			classes,
			onClick,
			blur,
		}
	},
})
