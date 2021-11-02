function buildUrl(
	baseUrl: string,
	path: string,
	query?: Record<string, any>,
	params?: Record<string, any>
): string {
	let url = baseUrl + path

	// Add query parameters to URL
	if (query !== undefined) {
		const params = new URLSearchParams()

		Object.keys(query)
			.filter((k: string) => {
				return query[k] !== undefined
			})
			.forEach((k: string) => {
				params.set(k, query[k])
			})

		url += `?${params.toString()}`
	}

	// Inject URL parameters (e.g. "/path/{id}")
	if (params !== undefined) {
		Object.keys(params).forEach((k) => {
			url = url.replace(`{${k}}`, params[k])
		})
	}

	return url
}

export default buildUrl
