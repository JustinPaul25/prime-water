const state = () => ({
    addresses: [],
    pagination: {
        meta: {
            last_page: 1
        }
    }
})

// getters
const getters = {
    addresses: (state) => {
        return state.addresses
    }
}

const mutations = {
    setAddresses (state, payload) {
        state.addresses = payload
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
    async getAddresses ({ commit }, payload = {}) {
        await axios.get(`/address-list`, {
            params: payload.params
        })
        .then(response => {
            console.log(response);
            commit('setAddresses', response.data)
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
