import {AuthState} from './modules/auth'
import {ColllectionState} from './modules/colllection'
import {ColllectionsState} from './modules/colllections'
import {WindowState} from './modules/window'

export interface RootState {
  auth: AuthState,
  colllection: ColllectionState,
  colllections: ColllectionsState,
  window: WindowState,
}
