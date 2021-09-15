import {defineComponent} from 'vue'

import {Tag} from '@/src/../generated/api'
import ColllectButton from '@/src/components/button/Button.vue'
import ColllectInput from '@/src/components/input/Input.vue'
import ColllectModal from '@/src/components/modal/Modal.vue'
import ElementTypes from '@/src/models/ElementTypes'

interface EnhancedTag extends Tag {
  color?: string
}

export default defineComponent({
	name: 'ColllectAddElement',
	components: {
		ColllectButton,
		ColllectInput,
		ColllectModal,
	},
	props: {
		show: {
			type: Boolean as () => boolean,
			default: false,
		},
	},
	emits: [
		'close',
	],
	setup(_props,  {emit}) {
		const elementTypes= Object.values(ElementTypes)
		const currentElementType = ElementTypes.Image
		const url = ''
		const name = ''
		const tags: EnhancedTag[] = [
			// {name: 'Landing', encodedName: ''},
			// {name: 'Red', encodedName: ''},
			// {name: 'UI', encodedName: ''},
			// {name: 'Typography', encodedName: ''},
			// {name: 'User experience', encodedName: ''},
		]
		const suggestedTags: EnhancedTag[] = [
			// {name: 'Shapes', encodedName: ''},
			// {name: 'Purple', encodedName: ''},
			// {name: 'Round', encodedName: ''},
		]

		const hideAddElementModal = (): void => {
			// TODO: add AddElement modal state to store and switch it to false
			emit('close')
		}

		return {
			elementTypes,
			currentElementType,
			url,
			name,
			tags,
			suggestedTags,
			hideAddElementModal,
		}
	},
})
