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
  methods: {},
  mounted() {},
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
    min-height: 100%;
}
.userInput textarea {
    min-height: 100% !important;
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
