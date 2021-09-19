import {acceptHMRUpdate, defineStore} from 'pinia'

import {UsersService} from '@/src/api'

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
		async loadCurrentUser() {
			return UsersService.getCurrentUser()
				.then((currentUser) => {
					this.id = currentUser.id as number
					this.nickname = currentUser.nickname
					this.roles = currentUser.roles as string[]
				})
		},
	},
})

export default useAuthStore

if (import.meta.hot) {
	import.meta.hot.accept(acceptHMRUpdate(useAuthStore, import.meta.hot))
}
