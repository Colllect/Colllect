import {ref} from 'vue'

import api, {Element} from '@/src/api'
import base64UriDecode from '@/src/functions/base64Uri'

// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
const useColllection = () => {
	const name = ref('')
	const isLoaded = ref(false)
	const elements = ref<Element[]>([])

	const loadColllection = async (encodedColllectionPath: string) => {
		name.value = base64UriDecode(encodedColllectionPath).split('/').pop() ?? ''
		isLoaded.value = false

		const loadColllectionPromise = api
			.getApiColllectionsByEncodedColllectionPath({encodedColllectionPath})
			.then((colllectionResponse) => {
				if (colllectionResponse.status !== 200) {
					return
				}

				name.value = colllectionResponse.body.name ?? ''
			})

		const loadColllectionElementsPromise = api
			.getApiColllectionsByEncodedColllectionPathElements({encodedColllectionPath})
			.then((elementsResponse) => {
				if (elementsResponse.status !== 200) {
					return
				}

				elements.value = elementsResponse.body
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
