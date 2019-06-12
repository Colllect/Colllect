import * as HttpStatus from 'http-status-codes'

import Api from '../generated/api'

const api = new Api()
api.addErrorHandler((err) => {
  if (err.hasOwnProperty('status') && err.status === HttpStatus.UNAUTHORIZED) {
    window.location.replace('/login')
  }
})

export * from '../generated/api'
export default api
