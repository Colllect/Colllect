import {acceptHMRUpdate, defineStore} from 'pinia'

export interface WindowState {
  scrollTop: number
  width: number
  height: number
}

const useWindowStore = defineStore({
	id: 'window',
	state: (): WindowState => {
		return {
			scrollTop: 0,
			width: 0,
			height: 0,
		}
	},
	actions: {
		windowScroll(scrollTop: number) {
			this.scrollTop = scrollTop
		},
		windowResize({width, height}: { width: number; height: number }) {
			this.$patch({
				width,
				height,
			})
		},
	},
})

export default useWindowStore

if (import.meta.hot) {
	import.meta.hot.accept(acceptHMRUpdate(useWindowStore, import.meta.hot))
}
