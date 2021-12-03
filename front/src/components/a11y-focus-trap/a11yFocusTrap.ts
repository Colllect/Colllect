import { defineComponent, onMounted, onUnmounted, ref } from 'vue'

interface TrapInfo {
	rootContainer: HTMLElement
  prevTarget: HTMLElement
}

const FOCUSABLE_SELECTOR = [
	'a[href]',
	'area[href]',
	'button',
	'details',
	'input',
	'iframe',
	'select',
	'textarea',
	'[contentEditable=""]',
	'[contentEditable="true"]',
	'[contentEditable="TRUE"]',
	'[tabindex]',
].map((selector) => selector + ':not([tabindex^="-"]):not([disabled])').join(',')

export default defineComponent({
	name: 'A11yFocusTrap',
	setup() {
		const rootContainer = ref<HTMLElement>()
		const startElement = ref<HTMLElement>()
		const endElement = ref<HTMLElement>()
		const focusableContainer = ref<HTMLElement>()
		const trapStack: TrapInfo[] = []

		const open = (): void => {
			if (rootContainer.value === undefined) {
				return
			}

			const prevTarget = document.activeElement as HTMLElement
			trapStack.push({
				rootContainer: rootContainer.value,
				prevTarget,
			})

			const autofocusElement = rootContainer.value.querySelector('[autofocus]') as HTMLElement
			if (autofocusElement) {
				autofocusElement.focus()
				return
			}
			goFirst()
		}

		const close = (returnFocus = true) => {
			const trap = trapStack.pop()
			if (trap === undefined) {
				return
			}
			const { prevTarget } = trap
			if (returnFocus) {
				prevTarget.focus()
			}
		}

		const goFirst = () => {
			const focusableElements = getFocusableElements()
			focusableElements[0].focus()
		}

		const goLast = () => {
			const focusableElements = getFocusableElements()
			focusableElements[focusableElements.length - 1].focus()
		}

		const getFocusableElements = (): HTMLElement[] => {
			if (focusableContainer.value === undefined) {
				return []
			}

			const focusableElements = Array.from(focusableContainer.value.querySelectorAll(FOCUSABLE_SELECTOR)) as HTMLElement[]

			return focusableElements
		}

		const trapFocus = (event: FocusEvent) => {
			const trap = trapStack[trapStack.length - 1]
			if (!trap || trap.rootContainer !== rootContainer.value) {
				return
			}

			const { target } = event
			if (!rootContainer.value?.contains(target as HTMLElement) || target === endElement.value) {
				event.preventDefault()
				goFirst()
				return
			}
			if (target === startElement.value) {
				event.preventDefault()
				goLast()
			}
		}

		onMounted(() => {
			document.addEventListener('focus', trapFocus, true)
			open()
		})

		onUnmounted(() => {
			close()
			document.removeEventListener('focus', trapFocus, true)
		})

		return {
			rootContainer,
			startElement,
			focusableContainer,
			endElement,
		}
	},
})
