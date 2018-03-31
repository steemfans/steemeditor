import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
  strict: debug,
  state: {
    content: null,
    tags: null,
    title: null,
  },
  mutations: {
    content(state, content) {
      state.content = content;
    },
    tags(state, tags) {
      state.tags = tags;
    },
    title(state, title) {
      state.title = title;
    },
  },
  getters: {
    content(state) {
      return state.content;
    },
    tags(state) {
      return state.tags;
    },
    title(state) {
      return state.title;
    },
  },
  plugins: [
    createPersistedState({
      storage: {
        getItem: key => localStorage.getItem(key),
        setItem: (key, value) => localStorage.setItem(key, value),
        removeItem: key => localStorage.removeItem(key),
      },
    }),
  ],
});
