import fetchApi, { ApiRequestBody } from '@/src/functions/fetchApi'
import { Element } from '@/src/types/api/definitions'

type ElementFormDataBody =
	Omit<ApiRequestBody<'/api/colllections/{encodedColllectionPath}/elements', 'post'>, 'file'>
	& { file?: File }

export class ColllectionElementService {
	public static getColllectionElements = (
		encodedColllectionPath: string,
	): Promise<Element[]> => {
		return fetchApi(
			'/api/colllections/{encodedColllectionPath}/elements',
			{
				params: {
					encodedColllectionPath,
				},
			},
		)
	}

	public static postColllectionElement = (
		encodedColllectionPath: string,
		body: ElementFormDataBody,
	): Promise<Element> => {
		return fetchApi(
			'/api/colllections/{encodedColllectionPath}/elements',
			{
				method: 'post',
				params: {
					encodedColllectionPath,
				},
				body,
			},
		)
	}

	public static getColllectionElement = (
		encodedColllectionPath: string,
		encodedElementBasename: string,
	): Promise<Element> => {
		return fetchApi(
			'/api/colllections/{encodedColllectionPath}/elements/{encodedElementBasename}',
			{
				params: {
					encodedColllectionPath,
					encodedElementBasename,
				},
			},
		)
	}

	public static putColllectionElement = (
		encodedColllectionPath: string,
		encodedElementBasename: string,
		body: ElementFormDataBody,
	): Promise<Element> => {
		return fetchApi(
			'/api/colllections/{encodedColllectionPath}/elements/{encodedElementBasename}',
			{
				method: 'put',
				params: {
					encodedColllectionPath,
					encodedElementBasename,
				},
				body,
			},
		)
	}

	public static deleteColllectionElement = (
		encodedColllectionPath: string,
		encodedElementBasename: string,
	): Promise<undefined> => {
		return fetchApi(
			'/api/colllections/{encodedColllectionPath}/elements/{encodedElementBasename}',
			{
				method: 'delete',
				params: {
					encodedColllectionPath,
					encodedElementBasename,
				},
			},
		)
	}
}

export default ColllectionElementService
