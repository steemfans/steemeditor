<template>
  <el-container id="app" direction="vertical">
    <top-nav></top-nav>
    <router-view/>
  </el-container>
</template>

<script>
import TopNav from './components/TopNavComponent.vue';

export default {
  name: 'App',
  data() {
    return {
    };
  },
  mounted() {
  },
  components: {
    TopNav,
  },
  methods: {},
};
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
</style>
