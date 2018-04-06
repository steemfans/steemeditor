<template>
  <el-container>
    <el-aside width="400px">
      <material-list :all-public="false" :height="asideHeight" @addMaterialMsg="handleAddMaterialMsg"></material-list>
    </el-aside>
    <el-main>
      <el-row class="toolbar">
        <el-col :span="6">
          Mode:
          <el-tag v-if="mode === 'Create'" size="mini">{{ mode }}</el-tag>
          <el-tag v-if="mode === 'Edit'" type="success" size="mini">{{ mode }}</el-tag>
        </el-col>
        <el-col :span="6" v-if="materialId">
          Material ID: {{ materialId }}
        </el-col>
        <el-col :span="6">
          <el-switch
            v-model="materialType"
            active-text="Public"
            inactive-text="Private">
          </el-switch>
        </el-col>
        <el-col :span="6">
          <el-button type="success" size="mini" @click="save">Save</el-button>
          <el-button type="danger" size="mini" v-if="mode === 'Edit'" @click="exitEdit">Exit Edit</el-button>
        </el-col>
      </el-row>
      <editor
        v-if="editorConfig !== null"
        ref="materialEditor"
        :editor-config="editorConfig"
        :insert-content="insertContent"
        @clearInsertContent="handleClearInsertContent"
        @materialChange="handleMaterialChange"
        :clear-content="clear"
        @resetClear="handleResetClear">
      </editor>
    </el-main>
  </el-container>
</template>
<script>
import axios from 'axios';
import MaterialList from './Material/ListComponent.vue';
import Editor from './EditorComponent.vue';

export default {
  name: 'material',
  data() {
    return {
      clear: false,
      mode: 'Create',
      materialId: null,
      materialType: true,
      insertContent: null,
      content: null,
      asideHeight: this.getAsideHeight(),
      editorConfig: null,
    };
  },
  components: {
    MaterialList,
    Editor,
  },
  mounted() {
    const refs = this.$refs;
    this.editorConfig = {
      vueInstance: true,
      width: '100%',
      height: this.getEditorHeight(),
      path: '/plugins/editor/lib/',
      codeFold: true,
      saveHTMLToTextarea: true,
      watch: true,
      searchReplace: true,
      placeholder: 'Manage your material !!!',
      previewCodeHighlight: false,
      emoji: true,
      toolbarIcons: () => [
        'undo', 'redo', '|',
        'bold', 'del', 'italic', 'quote', 'ucwords', 'uppercase', 'lowercase', '|',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '|',
        'list-ul', 'list-ol', 'hr', '|',
        'link', 'reference-link', 'image', 'code', 'code-block', 'table', 'pagebreak', '|',
        'goto-line', 'clear', 'search', 'preview', 'watch', 'fullscreen',
      ],
      onload: () => {
        window.consoleLog(['onload in material manager component config']);
      },
      onchange() {
        refs.materialEditor.$emit('materialChange', this.markdownTextarea[0].innerHTML);
      },
    };
  },
  methods: {
    init() {
      this.createMode();
      this.content = null;
    },
    createMode() {
      this.mode = 'Create';
    },
    editMode() {
      this.mode = 'Edit';
    },
    getAsideHeight() {
      const totalHeight = window.innerHeight;
      return `${String(totalHeight - 80)}px`;
    },
    getEditorHeight() {
      const totalHeight = window.innerHeight;
      return String(totalHeight - 150);
    },
    handleAddMaterialMsg(data) {
      window.consoleLog(['material component handleAddMaterialMsg', data]);
      this.editMode();
      this.insertContent = data.body;
    },
    handleClearInsertContent() {
      window.consoleLog(['clear insert content']);
      this.insertContent = null;
    },
    handleMaterialChange(data) {
      window.consoleLog(['update content']);
      this.content = data;
    },
    exitEdit() {
      this.createMode();
    },
    save() {
      window.consoleLog(['save material', this.content]);
      if (this.content === '' || this.content === null) {
        this.$notify.error({
          title: 'Error',
          message: 'Please input content.',
        });
        return;
      }
      const data = {
        title: null,
        body: this.content,
        tags: [],
        material_type: this.materialType,
        token: window.Laravel.accessToken,
      };
      const loading = this.$loading({
        lock: true,
        text: 'Sending',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)',
      });
      axios.post('/api/material', data)
        .then((res) => {
          window.consoleLog(['save material result', res]);
          loading.close();
          const r = res.data;
          if (r.status === false) {
            let msg = '';
            switch (r.msg) {
              case 'need_token':
                msg = 'Please login first.';
                break;
              default:
                msg = `Unknown error: ${res.msg}`;
                break;
            }
            this.$notify.error({
              title: 'Error',
              message: msg,
            });
          } else {
            this.clear = true;
            this.$notify({
              title: 'Success',
              message: 'Save successful.',
              type: 'success',
            });
          }
        })
        .catch((err) => {
          window.consoleLog([err, 'logout err']);
        });
    },
    handleResetClear() {
      this.clear = false;
    },
  },
};
</script>
<style scoped>
.toolbar {
  margin-bottom: 10px;
}
.toolbar .el-col {
  padding: 5px 0;
}
</style>
