<template>
  <el-container>
    <el-aside width="400px">
      <material-list
        :all-public="false"
        :height="asideHeight"
        :refresh="refresh"
        @addMaterialMsg="handleAddMaterialMsg"
        @handleRefresh="handleRefresh">
      </material-list>
    </el-aside>
    <el-main>
      <el-row class="toolbar">
        <el-col :span="4">
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
        <el-col :span="8">
          <el-button type="success" size="mini" v-if="mode === 'Create'" @click="addNew">Create</el-button>
          <el-button type="success" size="mini" v-if="mode === 'Edit'" @click="saveMaterial">Save</el-button>
          <el-button type="primary" size="mini" v-if="mode === 'Edit'" @click="exitEdit">Exit Edit</el-button>
          <el-button type="danger" size="mini" v-if="mode === 'Edit'" @click="deleteMaterial">Delete</el-button>
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
      replaceContent: null,
      content: null,
      asideHeight: this.getAsideHeight(),
      refresh: false,
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
      htmlDecode : "style,script,iframe|on*",
      searchReplace: true,
      placeholder: 'Manage your material !!!',
      previewCodeHighlight: false,
      emoji: true,
      toolbarIcons: () => [
        'undo', 'redo', '|',
        'bold', 'del', 'italic', 'quote', 'ucwords', 'uppercase', 'lowercase', '|',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '|',
        'list-ul', 'list-ol', 'hr', '|',
        'link', 'reference-link', 'image', 'code', 'code-block', 'table', '|',
        'goto-line', 'clear', 'search', 'watch', 'fullscreen',
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
      this.materialId = null;
      this.materialType = true;
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
      this.materialType = !!data.public;
      this.materialId = data.id;
      this.replaceInEditor(data.body);
    },
    handleClearInsertContent() {
      window.consoleLog(['clear insert content']);
      this.insertContent = null;
    },
    handleMaterialChange(data) {
      window.consoleLog(['update content']);
      this.content = data;
    },
    handleResetClear() {
      this.clear = false;
      if (this.replaceContent !== null) {
        this.insertContent = this.replaceContent;
        this.replaceContent = null;
      }
    },
    handleRefresh() {
      this.refresh = false;
    },
    exitEdit() {
      this.clearEditor();
      this.init();
    },
    clearEditor() {
      this.clear = true;
    },
    replaceInEditor(txt) {
      window.consoleLog(['replace', txt]);
      this.replaceContent = txt;
      this.clearEditor();
    },
    addNew() {
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
            this.clearEditor();
            this.$notify({
              title: 'Success',
              message: 'Save successful.',
              type: 'success',
            });
          }
          this.refresh = true;
        })
        .catch((err) => {
          window.consoleLog([err, 'logout err']);
        });
    },
    saveMaterial() {
      if (this.content === '' || this.content === null) {
        this.$notify.error({
          title: 'Error',
          message: 'Please input content.',
        });
        return;
      }
      const data = {
        m_id: this.materialId,
        token: window.Laravel.accessToken,
        body: this.content,
        type: this.materialType,
      };
      axios.post('/api/material/update', data)
        .then((res) => {
          const r = res.data;
          if (r.status === false) {
            this.$notify.error({
              title: 'Error',
              message: r.msg,
            });
          } else {
            this.$notify({
              title: 'Success',
              message: 'Save successful!',
              type: 'success',
            });
          }
          this.refresh = true;
        })
        .catch((err) => {
          window.consoleLog(['update material fail', err], true);
        });
    },
    deleteMaterial() {
      this.$confirm(
        'Material will be deleted. Do you want to continue?',
        'Notify',
        {
          confirmButtonText: 'Confirm',
          confirmButtonClass: 'el-button--danger',
          cancelButtonText: 'Cancel',
          type: 'danger',
        },
      ).then(() => {
        const data = {
          m_id: this.materialId,
          token: window.Laravel.accessToken,
        };
        axios.post('/api/material/remove', data)
          .then((res) => {
            const r = res.data;
            if (r.status === false) {
              this.$notify.error({
                title: 'Error',
                message: r.msg,
              });
            } else {
              this.$notify({
                title: 'Success',
                message: 'Delete success!',
                type: 'success',
              });
            }
            this.refresh = true;
            this.exitEdit();
          })
          .catch((err) => {
            window.consoleLog(['remove material fail', err], true);
          });
      });
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
