import { createStore, createLogger } from 'vuex'
import staffs from './modules/staffs'
import clients from './modules/client'

export default createStore({
  modules: {
    staffs,
    clients
  },
  strict: false,
  plugins: [createLogger()]
})