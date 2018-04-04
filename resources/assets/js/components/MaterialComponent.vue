<template>
  <el-container>
    <el-aside width="400px">
      <material-list :height="asideHeight"></material-list>
    </el-aside>
    <el-main>Main</el-main>
  </el-container>
</template>
<script>
import MaterialList from './Material/ListComponent.vue';

export default {
  name: 'material',
  data() {
    return {
      asideHeight: this.getAsideHeight(),
    };
  },
  components: {
    MaterialList,
  },
  mounted() {
  },
  methods: {
    getAsideHeight() {
      const totalHeight = window.innerHeight;
      return `${String(totalHeight - 80)}px`;
    },
  },
};
</script>
<style>
</style>
