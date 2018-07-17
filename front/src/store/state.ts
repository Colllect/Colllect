import {AuthState} from './modules/auth'
import {ColllectionState} from './modules/colllection'
import {ColllectionsState} from './modules/colllections'

export interface RootState {
  auth: AuthState,
  colllection: ColllectionState,
  colllections: ColllectionsState,
}
