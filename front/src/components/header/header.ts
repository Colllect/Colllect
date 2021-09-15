import {computed, defineComponent} from 'vue'

import authStore from '@/src/store/modules/auth'

export default defineComponent({
	setup() {
		const nickname = computed<string>(() => {
			return authStore.state.nickname ?? ''
		})

		const isAuthenticated = computed<boolean>(() => {
			return authStore.isAuthenticated
		})

		return {
			nickname,
			isAuthenticated,
		}
	},
})
