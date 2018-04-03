<template>
  <div :id="id" class="main-editor">
    <textarea v-model="initContent"></textarea>
  </div>
</template>
<script>
import $s from 'scriptjs';

export default {
  name: 'Editor',
  props: {
    id: {
      type: String,
      default: 'editor-md',
    },
    initContent: {
      type: String,
      default: '',
    },
    type: {
      type: String,
      default: 'editor',
    },
    editorPath: {
      type: String,
      default: '/plugins/editor/lib/',
    },
    width: {
      type: String,
      default: '100%',
    },
    height: {
      type: String,
      default: '560',
    },
    editorConfig: {
      type: Object,
      default() {
        const that = this;
        return {
          width: this.width,
          height: this.height,
          path: '/plugins/editor/lib/',
          codeFold: true,
          saveHTMLToTextarea: true,
          watch: true,
          searchReplace: true,
          placeholder: 'Start your creation !!!',
          // htmlDecode: 'style,script,iframe|on*',
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
            window.consoleLog(['onload', this.initContent]);
          },
          onchange() {
            that.$emit('contentChange', this.markdownTextarea[0].innerHTML);
          },
        };
      },
    },
  },
  data() {
    return {
      instance: null,
      content: null,
    };
  },
  mounted() {
    this.initEditorHeight();
    if (window.jQuery === undefined) {
      $s([
        'js/jquery-3.3.1.min.js',
      ], () => {
        $s(`${this.editorPath}editormd.min.js`, () => {
          $s([
            `${this.editorPath}../languages/en.js`,
          ], () => {
            this.initEditor();
          });
        });
      });
    }
  },
  destoryed() {
  },
  watch: {
  },
  methods: {
    initEditor() {
      this.$nextTick((editorMD = window.editormd) => {
        if (editorMD) {
          if (this.type === 'editor') {
            window.consoleLog(['editor']);
            this.instance = editorMD(this.id, this.editorConfig);
          } else {
            window.consoleLog(['html']);
            this.instance = editorMD.markdownToHTML(this.id, this.editorConfig);
          }
        }
      });
    },
  },
};
</script>
