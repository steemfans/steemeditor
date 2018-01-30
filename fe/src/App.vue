<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      userInfo: '',
      sc2: window.sc2,
    };
  },
  methods: {
    getUrlParam() {
      const obj = {};
      const url = window.location;
      obj.hash = url.hash.replace('#', '');
      obj.param = {};
      const arr = url.search.slice(1).split('&');
      for (let i = 0; i < arr.length; i += 1) {
        const temp = arr[i].split('=');
        obj.param[temp[0]] = temp[1];
      }
      return obj;
    },
  },
  mounted() {
    this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
    if (!this.userInfo) {
      const param = this.getUrlParam().param;
      if (param.access_token) {
        this.sc2.setAccessToken(param.access_token);
        this.sc2.me((err, result) => {
          this.consoleLog(result);
          if (!err) {
            localStorage.setItem('userInfo', result);
          }
        });
      } else {
        this.sc2.init({
          baseURL: 'https://v2.steemconnect.com',
          app: 'steemeditor',
          callbackURL: 'https://steemeditor.com',
          scope: ['vote', 'comment'],
        });
        const link = this.sc2.getLoginURL('test');
        window.location.href = link;
      }
    }
  },
};
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  /*margin-top: 60px;*/
}
.markDown textarea {
    height: 100%;
}
.userInput textarea {
    height: 100%;
}
.markDown h1 {
    display: block;
    font-size: 2em !important;
    -webkit-margin-before: 0.67em;
    -webkit-margin-after: 0.67em;
    -webkit-margin-start: 0px;
    -webkit-margin-end: 0px;
    font-weight: bold;
}
code {
  background-color: #eee;
  padding: 10px;
}
</style>
