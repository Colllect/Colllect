import { defineComponent, ref } from 'vue'

import ColllectButton from '@/src/components/button/Button.vue'
import ColllectInput from '@/src/components/input/Input.vue'
import ColllectModal from '@/src/components/modal/Modal.vue'
import ElementType from '@/src/enums/elementType'
import { Tag } from '@/src/types/api/definitions'

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
	setup(_props, { emit }) {
		const elementTypes = Object.values(ElementType)
		const currentElementType = ref(ElementType.Image)
		const url = ref('')
		const name = ref('')
		const tag = ref('')
		const tags = ref<EnhancedTag[]>([
			// {name: 'Landing', encodedName: ''},
			// {name: 'Red', encodedName: ''},
			// {name: 'UI', encodedName: ''},
			// {name: 'Typography', encodedName: ''},
			// {name: 'User experience', encodedName: ''},
		])
		const suggestedTags = ref<EnhancedTag[]>([
			// {name: 'Shapes', encodedName: ''},
			// {name: 'Purple', encodedName: ''},
			// {name: 'Round', encodedName: ''},
		])

		const hideAddElementModal = (): void => {
			// TODO: add AddElement modal state to store and switch it to false
			emit('close')
		}

		return {
			elementTypes,
			currentElementType,
			url,
			name,
			tag,
			tags,
			suggestedTags,
			hideAddElementModal,
		}
	},
})
