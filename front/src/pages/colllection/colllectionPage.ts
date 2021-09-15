import {computed, defineComponent} from 'vue'
import {useRoute} from 'vue-router'

import ColllectColllection from '@/src/components/colllection/Colllection.vue'

export default defineComponent({
	name: 'ColllectionPage',
	components: {
		ColllectColllection,
	},
  setup() {
		const route = useRoute()

		const encodedColllectionPath = computed<string>(() => {
			return route.params.encodedColllectionPath as string
		})

		return {
			encodedColllectionPath,
		}
	},
})
