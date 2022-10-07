const state = () => ({
    staffs: [],
    pagination: {
        meta: {
            last_page: 1
        }
    }
})

// getters
const getters = {
    staffs: (state) => {
        return state.staffs
    }
}

const mutations = {
    setStaffs (state, payload) {
        state.staffs = payload
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
    async getStaffs ({ commit }, payload = {}) {
        await axios.get(`/staff-list`, {
            params: payload.params
        })
        .then(response => {
            commit('setStaffs', response.data)
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