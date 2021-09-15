import {debounce, throttle} from 'lodash-es'
import {computed, defineComponent, onMounted, ref} from 'vue'

import ColllectAddElement from '@/src/components/add-element/AddElement.vue'
import ColllectHeader from '@/src/components/header/Header.vue'
import authStore from '@/src/store/modules/auth'
import windowStore from '@/src/store/modules/window'

export default defineComponent({
	name: 'App',
	components: {
		ColllectAddElement,
		ColllectHeader,
	},
	setup() {
		const showAddElementModal = ref(true)

		const isAuthenticated = computed<boolean>(() => {
			return authStore.isAuthenticated
		})

		const scrollableNode = computed<HTMLElement>(() => {
			return document.querySelector('.m-app--main') as HTMLElement
		})

		const handleScroll = () => {
			windowStore.dispatchWindowScroll(scrollableNode.value.scrollTop)
		}

		const handleResize = () => {
			windowStore.dispatchWindowResize({
				width: window.innerWidth,
				height: window.innerHeight,
			})
		}

		onMounted(() => {
			authStore.dispatchGetCurrentUser()

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
