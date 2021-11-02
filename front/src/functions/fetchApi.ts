import buildUrl from '@/src/functions/buildUrl'
import {paths} from '@/src/generated/apiTypes'
import Get from '@/src/types/utilities/get'
import KeysOfUnion from '@/src/types/utilities/keysOfUnion'
import PickDefined from '@/src/types/utilities/pickDefined'

type Paths = keyof paths;

type HTTPSuccess = 200 | 201 | 204;

type Methods = KeysOfUnion<paths[keyof paths]>;

type ApiResponse<Path, Method> = Get<paths,
	[Path, Method, 'responses', HTTPSuccess, 'schema']>;

type ApiParam<Path, Method, Parameter> = Get<paths,
	[Path, Method, 'parameters', Parameter]>;

export type ApiRequestBody<Path, Method> = Get<paths,
	[Path, Method, 'parameters', 'formData']>;

type FetchOptions<Path, Method> = Omit<RequestInit, 'body'> & {
	method?: Method
	headers?: Record<string, string>
} & PickDefined<{
	query: ApiParam<Path, Method, 'query'>
	params: ApiParam<Path, Method, 'path'>
	body: ApiRequestBody<Path, Method>
}>;

function fetchApi<Path extends Paths, Method extends Methods = 'get'>(
	path: Path,
	options?: FetchOptions<Path, Method>,
): Promise<ApiResponse<Path, Method>> {
	const fetchOptions = {
		headers: {},
		...options,
	} as RequestInit & {
		body?: Get<FetchOptions<Path, Method>, ['body']>
		headers: Record<string, string>
		query?: Record<string, any>
		params?: Record<string, any>
	}

	const url = buildUrl(
		'/',
		path,
		fetchOptions.query,
		fetchOptions.params,
	)
	delete fetchOptions.query
	delete fetchOptions.params

	fetchOptions.headers['Accept'] = 'application/json'

	return fetch(url, fetchOptions).then((r) => {
		if (r.status === 204) {
			return null
		}

		if (r.status >= 200 && r.status < 300) {
			return r.json()
		}

		throw r
	})
}

export default fetchApi
