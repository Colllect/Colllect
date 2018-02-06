import {AuthState} from './modules/auth'
import {CollectionState} from './modules/collection'

export interface RootState {
  auth: AuthState,
  collection: CollectionState,
}
