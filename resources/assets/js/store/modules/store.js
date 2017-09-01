const stores = {
  state: {
    stores: []
  },
  mutaions: {
    SET_STORE_LIST: (state, stores) => {
      state.stores = stores
    }
  },
  actions: {
    GetStoreList({ commit, state }) {
      if (state.stores) {
        return state.stroes;
      }
      return new Promise((resolve, reject) => {
        fetchList({}).then(response => {
          const stores = response.data;
          commit('SET_STORE_LIST', stores);
          resolve(response);
        }).catch(error => {
          reject(error);
        })
      })
    }
  }
}

export default stores;
