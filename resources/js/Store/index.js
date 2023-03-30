import { createLogger, createStore } from 'vuex'
import addresses from './modules/address'
import clients from './modules/client'
import staffs from './modules/staffs'

export default createStore({
  modules: {
    staffs,
    clients,
    addresses
  },
  strict: false,
  plugins: [createLogger()]
})
