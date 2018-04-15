import {AuthState} from './modules/auth'
import {CollectionState} from './modules/collection'
import {CollectionsState} from './modules/collections'

export interface RootState {
  auth: AuthState,
  collection: CollectionState,
  collections: CollectionsState,
}
