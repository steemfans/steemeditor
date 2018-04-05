<template>
  <el-row class="material-list" ref="material_list">
    <el-col :span="24">
      <el-card class="card">
        <div class="preview"
          @click="addMaterialToEditor"
          @mouseleave="mouseOutCard"
          @mouseenter="mouseOverCard"
          v-html="testHtml">
        </div>
      </el-card>
    </el-col>
  </el-row>
</template>
<script>
import marked from 'marked';

const tt = "**感谢你的阅读，我是中文区见证人之一，欢迎通过 [SteemConnect](https://v2.steemconnect.com/sign/account-witness-vote?witness=ety001&amp;approve=1) 来给我投票，或者打开 &lt;https://steemit.com/~witnesses&gt; 页面，输入 *ety001* 进行投票。** ![2.gif](https://steemitimages.com/DQmZfJo3F8NXpFx7nenQA3zTU9jg6YoRQvfyeeHgSYpdqr4/2.gif) **中文区的见证人目前有：**\n\n 支持一下他们（按字母顺序），*一人可以有30票*： - @abit ｜ [投票](https://steemconnect.com/sign/account_witness_vote?approve=1&amp;witness=abit) - @bobdos｜ [投票](https://steemconnect.com/sign/account_witness_vote?approve=1&amp;witness=bobdos) - @ety001｜ [投票](https://steemconnect.com/sign/account_witness_vote?approve=1&amp;witness=ety001) - @justyy ｜[投票](https://steemconnect.com/sign/account_witness_vote?approve=1&amp;witness=justyy) - @skenan ｜[投票](https://steemconnect.com/sign/account_witness_vote?approve=1&amp;witness=skenan) --- **Thank you for reading. I'm a witness. I would really appreciate your witness vote! You can vote by [SteemConnect](https://v2.steemconnect.com/sign/account-witness-vote?witness=ety001&amp;approve=1). Or open &lt;https://steemit.com/~witnesses&gt; page, input *ety001* to vote.** ![2.gif](https://steemitimages.com/DQmZfJo3F8NXpFx7nenQA3zTU9jg6YoRQvfyeeHgSYpdqr4/2.gif)";
// fixing marked XSS vulnerability
marked.setOptions({ sanitize: true });
export default {
  name: 'material-list-detail',
  props: {
    mtype: String,
  },
  data() {
    return {
      testHtml: null,
    };
  },
  mounted() {
    this.testHtml = marked(tt);
    this.$nextTick(() => {
      this.preventEl();
    });
  },
  methods: {
    getData() {
    },
    mouseOutCard(e) {
      e.target.parentElement.style.cssText = '';
    },
    mouseOverCard(e) {
      e.target.parentElement.style.cssText = 'background-color: #eeefff';
    },
    addMaterialToEditor(e) {
      window.consoleLog(['click to add']);
      e.cancelBubble = true;
      e.stopPropagation();
      this.$emit('addMaterialMsg', { message: e });
    },
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
  },
};
</script>
<style scoped>
.card {
  padding: 0;
  margin-bottom: 20px;
  cursor: pointer;
}
.preview {
  width: 100%;
  overflow: scroll;
}
.material-list {
  overflow: scroll;
}
</style>
