import {acceptHMRUpdate, defineStore} from 'pinia'

import api from '@/src/api'

interface AuthState {
	id: number | null
	nickname: string | null
	roles: string[]
}

const useAuthStore = defineStore({
	id: 'auth',
	state: (): AuthState => {
		return {
			id: null,
			nickname: null,
			roles: [],
		}
	},
	getters: {
		isAuthenticated: (state) => {
			return state.nickname !== null
		},
	},
	actions: {
		getCurrentUser() {
			return api.getApiUsersCurrent({})
				.then((currentUserResponse) => {
					if (currentUserResponse.status !== 200) {
						return
					}

					this.id = currentUserResponse.body.id as number
					this.nickname = currentUserResponse.body.nickname
					this.roles = currentUserResponse.body.roles as string[]
				})
		},
	},
})

export default useAuthStore

if (import.meta.hot) {
	import.meta.hot.accept(acceptHMRUpdate(useAuthStore, import.meta.hot))
}
