const drivers = {
  state: {
    drivers: []
  },
  mutaions: {
    SET_DRIVER_LIST: (state, drivers) => {
      state.drivers = drivers
    }
  },
  actions: {
    GetDriverList({ commit, state }) {
      if (state.drivers) {
        return state.drivers;
      }
      return new Promise((resolve, reject) => {
        fetchList({}).then(response => {
          const drivers = response.data;
          commit('SET_DRIVER_LIST', drivers);
          resolve(response);
        }).catch(error => {
          reject(error);
        })
      })
    }
  }
}

export default drivers;
