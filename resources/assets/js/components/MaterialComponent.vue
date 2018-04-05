<template>
  <el-container>
    <el-aside width="400px">
      <material-list :height="asideHeight" @addMaterialMsg="handleAddMaterialMsg"></material-list>
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
        @materialChange="handleMaterialChange">
      </editor>
    </el-main>
  </el-container>
</template>
<script>
import MaterialList from './Material/ListComponent.vue';
import Editor from './EditorComponent.vue';

export default {
  name: 'material',
  data() {
    return {
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
        window.consoleLog(['onload in material manager component config', this]);
      },
      onchange() {
        refs.materialEditor.$emit('materialChange', this.markdownTextarea[0].innerHTML);
      },
    };
  },
  methods: {
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
      this.insertContent = data.md;
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
