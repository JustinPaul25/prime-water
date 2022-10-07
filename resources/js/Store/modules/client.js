const state = () => ({
    clients: [],
    pagination: {
        meta: {
            last_page: 1
        }
    }
})

// getters
const getters = {
    staffs: (state) => {
        return state.clients
    }
}

const mutations = {
    setClients (state, payload) {
        state.clients = payload
    },
    setPagination (state, payload) {
        if (payload == {}) {
            state.pagination = {
                meta: {
                    last_page: 1
                }
            }
        } else {
            state.pagination = payload
        }
    },
}

// actions
const actions = {
    async getClients ({ commit }, payload = {}) {
        await axios.get(`/client-list`, {
            params: payload.params
        })
        .then(response => {
            commit('setClients', response.data)
            commit('setPagination', response.data)
        })
    }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}