import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
  strict: debug,
  state: {
    logStatus: false,
    userInfo: {},
    content: null,
    tags: null,
    title: null,
    postQueueData: {},
  },
  mutations: {
    logStatus(state, logStatus) {
      state.logStatus = logStatus;
    },
    userInfo(state, userInfo) {
      state.userInfo = userInfo;
    },
    content(state, content) {
      state.content = content;
    },
    tags(state, tags) {
      state.tags = tags;
    },
    title(state, title) {
      state.title = title;
    },
    postQueueData(state, postData) {
      state.postQueueData = postData;
    },
  },
  getters: {
    logStatus(state) {
      return state.logStatus;
    },
    userInfo(state) {
      return state.userInfo;
    },
    content(state) {
      return state.content;
    },
    tags(state) {
      return state.tags;
    },
    title(state) {
      return state.title;
    },
    postQueueData(state) {
      return state.postQueueData;
    },
  },
  plugins: [
    createPersistedState({
      storage: {
        getItem: key => window.localStorage.getItem(key),
        setItem: (key, value) => window.localStorage.setItem(key, value),
        removeItem: key => window.localStorage.removeItem(key),
      },
    }),
  ],
});
