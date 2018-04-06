<template>
  <el-container v-loading="loading" v-bind:style="{ height: height }">
    <el-main>
      <el-row v-loading="loading">
        <el-col :span="24">
          <el-tabs v-model="currentTab" @tab-click="handleClick">
            <el-tab-pane label="Public" name="publicMaterial">
              <list-detail
                :id="'publicM'"
                :detail="publicMaterials"
                @addMaterialMsg="handleAddMaterialMsg"
                @pageChange="handlePageChange">
              </list-detail>
            </el-tab-pane>
            <el-tab-pane label="Private" name="privateMaterial">
              <list-detail
                :id="'privateM'"
                :detail="privateMaterials"
                @addMaterialMsg="handleAddMaterialMsg"
                @pageChange="handlePageChange">
              </list-detail>
            </el-tab-pane>
          </el-tabs>
        </el-col>
      </el-row>
    </el-main>
  </el-container>
</template>
<script>
import axios from 'axios';
import listDetail from './ListDetailComponent.vue';

export default {
  name: 'material-list',
  props: {
    height: String,
    allPublic: Boolean,
  },
  data() {
    return {
      loading: false,
      currentTab: 'publicMaterial',
      publicMaterials: [],
      privateMaterials: [],
      publicPage: 1,
      privatePage: 1,
    };
  },
  mounted() {
    this.loadPublicMaterial(1);
    this.loadPrivateMaterial(1);
  },
  components: {
    listDetail,
  },
  methods: {
    loadPublicMaterial(page) {
      // load public materials
      this.loading = true;
      const data = {
        page,
      };
      if (this.allPublic === false) {
        data.userid = window.Laravel.userid;
      }
      axios.get('/api/materials', { params: data })
        .then((res) => {
          this.loading = false;
          const r = res.data;
          this.publicMaterials = r.data;
        })
        .catch((err) => {
          window.consoleLog([err, 'logout err']);
        });
    },
    loadPrivateMaterial(page) {
      this.loading = true;
      const data = {
        type: 0,
        userid: window.Laravel.userid,
        token: window.Laravel.accessToken,
        page,
      };
      axios.get('/api/materials', { params: data })
        .then((res) => {
          this.loading = false;
          const r = res.data;
          this.privateMaterials = r.data;
        })
        .catch((err) => {
          window.consoleLog([err, 'logout err']);
        });
    },
    handleAddMaterialMsg(data) {
      window.consoleLog(['list component handleAddMaterialMsg', data]);
      this.$emit('addMaterialMsg', data);
    },
    handleClick(e) {
      window.consoleLog(['handleClick', e]);
    },
    handlePageChange(data) {
      if (data.id === 'publicM') {
        this.loadPublicMaterial(data.page);
      } else {
        this.loadPrivateMaterial(data.page);
      }
    },
  },
};
</script>
<style></style>
