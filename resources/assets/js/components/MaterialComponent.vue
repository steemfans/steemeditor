<template>
  <el-container>
    <el-aside width="400px">
      <material-list :height="asideHeight" @addMaterialMsg="handleAddMaterialMsg"></material-list>
    </el-aside>
    <el-main>
      <editor :editor-config="editorConfig" :insert-content="insertContent" @clearInsertContent="clearInsertContent"></editor>
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
      insertContent: null,
      asideHeight: this.getAsideHeight(),
      editorConfig: {
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
      },
    };
  },
  components: {
    MaterialList,
    Editor,
  },
  mounted() {
  },
  methods: {
    getAsideHeight() {
      const totalHeight = window.innerHeight;
      return `${String(totalHeight - 80)}px`;
    },
    getEditorHeight() {
      const totalHeight = window.innerHeight;
      return String(totalHeight - 60 - 150 - 60);
    },
    handleAddMaterialMsg(data) {
      window.consoleLog(['material component handleAddMaterialMsg', data]);
      this.insertContent = data.md;
    },
    clearInsertContent() {
      this.insertContent = null;
    },
  },
};
</script>
<style>
</style>
