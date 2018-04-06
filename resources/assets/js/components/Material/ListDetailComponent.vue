<template>
  <el-row class="material-list" ref="material_list">
    <el-col :span="24" v-for="(val,index) in materials" :key="val.id">
      <el-card class="card">
        <div class="preview"
          @click="addMaterialToEditor(index)"
          @mouseleave="mouseOutCard"
          @mouseenter="mouseOverCard"
          v-html="val.innerHTML">
        </div>
      </el-card>
    </el-col>
    <el-col v-if="!materials" :span="24">No data</el-col>
    <el-col v-if="materials" :span="24">
      <el-pagination
        background
        @current-change="handleCurrentChange"
        :current-page.sync="pageInfo.currentPage"
        :page-size="pageInfo.perPage"
        layout="prev, pager, next"
        :total="pageInfo.total">
      </el-pagination>
    </el-col>
  </el-row>
</template>
<script>
import marked from 'marked';

// fixing marked XSS vulnerability
marked.setOptions({ sanitize: true });
export default {
  name: 'material-list-detail',
  props: ['detail', 'id'],
  data() {
    return {
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.preventEl();
    });
  },
  computed: {
    materials() {
      if (this.detail.data) {
        this.detail.data.forEach((v, k) => {
          this.detail.data[k].innerHTML = marked(v.body);
        });
      }
      return this.detail.data;
    },
    pageInfo() {
      return {
        currentPage: this.detail.current_page,
        firstPageUrl: this.detail.first_page_url,
        from: this.detail.from,
        lastPage: this.detail.last_page,
        lastPageUrl: this.detail.last_page_url,
        nextPageUrl: this.detail.next_page_url,
        path: this.detail.path,
        perPage: this.detail.per_page,
        prevPageUrl: this.detail.prev_page_url,
        to: this.detail.to,
        total: this.detail.total,
      };
    },
  },
  methods: {
    mouseOutCard(e) {
      e.target.parentElement.style.cssText = '';
    },
    mouseOverCard(e) {
      e.target.parentElement.style.cssText = 'background-color: #eeefff';
    },
    addMaterialToEditor(index) {
      window.consoleLog(['click to add', index]);
      this.$emit('addMaterialMsg', this.materials[index]);
    },
    // remove <a> click event in marketdown
    preventEl() {
      const materialEl = this.$refs.material_list.$el;
      const a = materialEl.getElementsByTagName('a');
      const len = a.length;
      for (let i = 0; i < len; i += 1) {
        a[i].onclick = (e) => {
          e.preventDefault();
        };
      }
    },
    handleCurrentChange(page) {
      window.consoleLog(['pagination current change', page]);
      this.$emit('pageChange', { id: this.id, page });
    },
  },
  filters: {
    marked(val) {
      if (!val) return '';
      return marked(val);
    },
  },
};
</script>
<style>
.material-list {
  overflow: scroll;
}
.card {
  padding: 0;
  margin-bottom: 20px;
  cursor: pointer;
}
.el-card__body {
  padding: 5px;
}
.preview {
  width: 100%;
  overflow: scroll;
  height: 200px;
}
.preview * {
  height: 160px;
}
</style>
