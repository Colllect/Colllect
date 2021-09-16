import {StatusCodes} from 'http-status-codes'

import Api from '@/src/generated/api'

const api = new Api()
api.addErrorHandler((err) => {
  if (err?.status === StatusCodes.UNAUTHORIZED) {
    window.location.replace('/login')
  }
})

export * from '@/src/generated/api'
export default api
