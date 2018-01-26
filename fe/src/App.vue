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
    };
  },
  methods: {
    getUrlParam: function() {
      var obj = {};
      var url = window.location;
      obj.hash = url.hash.replace('#', '');
      obj.param = {};
      var arr = url.search.slice(1).split('&');
      for (var i = 0; i < arr.length; i++) {
        var temp = arr[i].split('=');
        obj.param[temp[0]] = temp[1];
      }
      return obj;
    },
  },
  mounted () {
    this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
    if (!this.userInfo) {
      var param = this.getUrlParam().param;
      if (param.access_token) {
        sc2.setAccessToken(param.access_token);
        sc2.me(function (err, result) {
          console.log('/me', err, result);
          if (!err) {
            localStorage.setItem('userInfo', result)
          }
        });
      } else {
        sc2.init({
          baseURL: 'https://v2.steemconnect.com',
          app: 'busy.app',
          callbackURL: 'http://test.com',
          scope: ['vote', 'comment']
        });
        var link = sc2.getLoginURL('test');
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
