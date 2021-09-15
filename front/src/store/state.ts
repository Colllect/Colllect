import {AuthState} from '@/src/store/modules/auth'
import {ColllectionState} from '@/src/store/modules/colllection'
import {ColllectionsState} from '@/src/store/modules/colllections'
import {WindowState} from '@/src/store/modules/window'

export interface RootState {
  auth: AuthState
  colllection: ColllectionState
  colllections: ColllectionsState
  window: WindowState
}
