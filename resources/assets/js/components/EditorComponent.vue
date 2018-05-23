<template>
  <div :id="id" class="main-editor" @paste="handlePaste">
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
    insertContent: {
      type: String,
      default: '',
    },
    clearContent: {
      type: Boolean,
      default: false,
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
          atLink: false,
          emailLink: false,
          width: this.width,
          height: this.height,
          path: '/plugins/editor/lib/',
          codeFold: true,
          saveHTMLToTextarea: true,
          watch: true,
          searchReplace: true,
          placeholder: 'Start your creation !!!',
          htmlDecode: 'style,script,iframe|on*',
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
          imageUpload: true,
          imageFormats: ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'],
          imageUploadURL: '/api/file/upload',
          onload: () => {
            window.consoleLog(['onload editor component']);
          },
          onchange() {
            that.$emit('contentChange', this.markdownTextarea[0].innerText);
          },
        };
      },
    },
  },
  data() {
    return {
      instance: null,
    };
  },
  mounted() {
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
    } else {
      this.initEditor();
    }
  },
  destoryed() {
  },
  watch: {
    insertContent(val) {
      if (val !== null) {
        window.consoleLog(['insert content to editor', val]);
        this.instance.insertValue(val);
        this.instance.focus();
        this.$emit('clearInsertContent');
      }
    },
    clearContent(val) {
      window.consoleLog(['clear editor content', val]);
      if (val === true) {
        window.jQuery.proxy(this.instance.toolbarHandlers.clear, this.instance)();
        this.$emit('resetClear');
      }
    },
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
    test() {
      window.consoleLog(['test']);
    },
    handlePaste(e) {
      window.consoleLog(['handle paste', e]);
      // chrome
      if (e.clipboardData && e.clipboardData.items[0].type.indexOf('image') > -1) {
        const file = e.clipboardData.items[0].getAsFile();
        const xhr = new XMLHttpRequest();
        const fd = new FormData();
        xhr.open('POST', '/api/file/upload', true);
        xhr.onload = () => {
          const res = JSON.parse(xhr.responseText);
          window.consoleLog(['xhr_res', res]);
          if (res.success === 1) {
            const imgMD = `![](${res.url})`;
            this.instance.insertValue(imgMD);
            this.instance.focus();
          }
        };
        fd.append('editormd-image-file', file); // this.result得到图片的base64
        xhr.send(fd);
      }
    },
  },
};
</script>
