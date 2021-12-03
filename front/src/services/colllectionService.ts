import fetchApi, { ApiRequestBody } from '@/src/functions/fetchApi'
import { Colllection } from '@/src/types/api/definitions'

class ColllectionService {
	public static getColllections = (): Promise<Colllection[]> => {
		return fetchApi(
			'/api/colllections'
		)
	}

	public static postColllection = (
		body: ApiRequestBody<'/api/colllections', 'post'>,
	): Promise<Colllection> => {
		return fetchApi(
			'/api/colllections',
			{
				method: 'post',
				body,
			}
		)
	}

	public static getColllection = (
		encodedColllectionPath: string,
	): Promise<Colllection> => {
		return fetchApi(
			'/api/colllections/{encodedColllectionPath}',
			{
				params: {
					encodedColllectionPath,
				},
			}
		)
	}

	public static putColllection = (
		encodedColllectionPath: string,
		body: ApiRequestBody<'/api/colllections', 'put'>,
	): Promise<Colllection> => {
		return fetchApi(
			'/api/colllections/{encodedColllectionPath}',
			{
				method: 'put',
				params: {
					encodedColllectionPath,
				},
				body,
			}
		)
	}

	public static deleteColllection = (
		encodedColllectionPath: string,
	): Promise<Colllection> => {
		return fetchApi(
			'/api/colllections/{encodedColllectionPath}',
			{
				method: 'delete',
				params: {
					encodedColllectionPath,
				},
			}
		)
	}
}

export default ColllectionService
