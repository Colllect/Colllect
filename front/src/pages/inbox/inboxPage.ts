import { defineComponent } from 'vue'

import ColllectColllection from '@/src/components/colllection/Colllection.vue'

export default defineComponent({
	name: 'InboxPage',
	components: {
		ColllectColllection,
	},
	setup() {
		const COLLECTION_NAME = 'Inbox'
		const encodedColllectionPath = encodeURIComponent(btoa(COLLECTION_NAME))

		return {
			encodedColllectionPath,
		}
	},
})
