import {
	ColllectionElementsService as GeneratedColllectionElementsService,
	ColllectionsService as GeneratedColllectionsService, OpenAPI,
	UsersService as GeneratedUsersService,
} from '@/src/generated/api'

OpenAPI.BASE = window.location.origin

export class UsersService {
	public static getCurrentUser = GeneratedUsersService.getUsersService1
}

export class ColllectionsService {
	public static getColllections = GeneratedColllectionsService.getColllectionsService
	public static postColllection = GeneratedColllectionsService.postColllectionsService
	public static getColllection = GeneratedColllectionsService.getColllectionsService1
	public static putColllection = GeneratedColllectionsService.putColllectionsService
	public static deleteColllection = GeneratedColllectionsService.deleteColllectionsService
}

export class ColllectionElementsService {
	public static getColllectionElements = GeneratedColllectionElementsService.getColllectionElementsService
	public static postColllectionElement = GeneratedColllectionElementsService.postColllectionElementsService
	public static getColllectionElement = GeneratedColllectionElementsService.getColllectionElementsService1
	public static putColllectionElement = GeneratedColllectionElementsService.putColllectionElementsService
	public static deleteColllectionElement = GeneratedColllectionElementsService.deleteColllectionElementsService
}

export * from '@/src/generated/api'
