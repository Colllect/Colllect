import { ref } from 'vue'

import base64UriDecode from '@/src/functions/base64Uri'
import ColllectionElementService from '@/src/services/colllectionElementService'
import ColllectionService from '@/src/services/colllectionService'
import { Element } from '@/src/types/api/definitions'

// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
const useColllection = () => {
	const name = ref('')
	const isLoaded = ref(false)
	const elements = ref<Element[]>([])

	const loadColllection = async (encodedColllectionPath: string) => {
		name.value = base64UriDecode(encodedColllectionPath).split('/').pop() ?? ''
		isLoaded.value = false

		const loadColllectionPromise = ColllectionService.getColllection(encodedColllectionPath)
			.then((colllection) => {
				name.value = colllection.name ?? ''
			})

		const loadColllectionElementsPromise = ColllectionElementService.getColllectionElements(encodedColllectionPath)
			.then((elementsList) => {
				elements.value = elementsList
			})

		await Promise.all([
			loadColllectionPromise,
			loadColllectionElementsPromise,
		])
		isLoaded.value = true
	}

	return {
		name,
		isLoaded,
		elements,
		loadColllection,
	}
}

export default useColllection
