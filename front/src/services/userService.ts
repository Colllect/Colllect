import fetchApi from '@/src/functions/fetchApi'
import {CurrentUser} from '@/src/types/api/definitions'

export class UserService {
	public static getCurrentUser = (): Promise<CurrentUser> => {
		return fetchApi(
			'/api/users/current',
		)
	}
}

export default UserService
