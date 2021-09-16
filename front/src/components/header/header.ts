import {computed, defineComponent} from 'vue'

import useAuthStore from '@/src/stores/auth'

export default defineComponent({
	setup() {
		const authStore = useAuthStore()

		const nickname = computed<string>(() => {
			return authStore.nickname ?? ''
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
