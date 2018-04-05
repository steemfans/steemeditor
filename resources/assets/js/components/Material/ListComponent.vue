<template>
  <el-container v-loading="loading" v-bind:style="{ height: height }">
    <el-main>
      <el-row>
        <el-col :span="24">
          <el-tabs v-model="currentTab" @tab-click="handleClick">
            <el-tab-pane label="Public" name="publicMaterial">
              <list-detail :mtype="'public'" @addMaterialMsg="handleAddMaterialMsg"></list-detail>
            </el-tab-pane>
            <el-tab-pane label="Private" name="privateMaterial">
              <list-detail :mtype="'private'" @addMaterialMsg="handleAddMaterialMsg"></list-detail>
            </el-tab-pane>
          </el-tabs>
        </el-col>
      </el-row>
    </el-main>
  </el-container>
</template>
<script>
import listDetail from './ListDetailComponent.vue';

export default {
  name: 'material-list',
  props: {
    height: String,
  },
  data() {
    return {
      loading: false,
      currentTab: 'publicMaterial',
    };
  },
  mounted() {
  },
  components: {
    listDetail,
  },
  methods: {
    loadMaterial() {
    },
    handleAddMaterialMsg(data) {
      window.consoleLog(['list component handleAddMaterialMsg', data]);
      this.$emit('addMaterialMsg', data);
    },
    handleClick(e) {
      window.consoleLog([e]);
    },
  },
};
</script>
<style></style>
