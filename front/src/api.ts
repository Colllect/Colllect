import Api from '../generated/api'
import auth from './store/modules/auth'

const api = new Api('http://localhost:8080')
api.addRequestHandler((req) => {
  if (auth.jwt != null && auth.jwt.length > 0) {
    req.set('Authorization', `Bearer ${auth.jwt}`)
  }
})

export * from '../generated/api'
export default api
