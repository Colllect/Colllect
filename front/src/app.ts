import {debounce, throttle} from 'lodash-es'
import {computed, defineComponent, onMounted, ref} from 'vue'

import ColllectAddElement from '@/src/components/add-element/AddElement.vue'
import ColllectHeader from '@/src/components/header/Header.vue'
import useAuthStore from '@/src/stores/auth'
import useWindowStore from '@/src/stores/window'

export default defineComponent({
	name: 'App',
	components: {
		ColllectAddElement,
		ColllectHeader,
	},
	setup() {
		const showAddElementModal = ref(false)
		const authStore = useAuthStore()
		const windowStore = useWindowStore()

		const isAuthenticated = computed<boolean>(() => {
			return authStore.isAuthenticated
		})

		const scrollableNode = computed<HTMLElement>(() => {
			return document.querySelector('.m-app--main') as HTMLElement
		})

		const handleScroll = () => {
			windowStore.windowScroll(scrollableNode.value.scrollTop)
		}

		const handleResize = () => {
			windowStore.windowResize({
				width: window.innerWidth,
				height: window.innerHeight,
			})
		}

		onMounted(() => {
			authStore.loadCurrentUser()

			scrollableNode.value.addEventListener('scroll', throttle(handleScroll, 300, {leading: false}))
			window.addEventListener('resize', debounce(handleResize, 300, {leading: true}))
		})

		return {
			showAddElementModal,
			isAuthenticated,
			scrollableNode,
			handleScroll,
			handleResize,
		}
	},
})
