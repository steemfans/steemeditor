<template>
  <div class="hello">
    <el-menu
    class="el-menu-demo"
    mode="horizontal"
    @select="handleSelect"
    text-color="#333"
    active-text-color="#1FBF8F"
    :default-active="activeIndex">
      <div class="logo_box">
        <span class="site_title">SteemEditor</span>
      </div>
      <!-- <el-menu-item index="1">SteemEditor</el-menu-item> -->
      <div class="login_box">
        <span class="login_info">{{ userName }}</span>
        <el-button :type="(logStatus ? 'danger' : 'primary')"
        @click="logStatusChange">{{ logStatus ? 'Logout' : 'Login' }}</el-button>
      </div>
    </el-menu>
    <div class="body_box">
      <div class="text_info">
        <el-input v-model="title" @change="titleChange" placeholder="Your Title"></el-input>
        <el-input v-model="tag" @change="tagChange"
        placeholder="Tags (separate by space)"></el-input>
      </div>
      <el-container style="border: 1px solid #dcdfe6;">
      <el-header>
        <el-dropdown>
          <el-button type="button">
            <i class="iconfont icon-zihao"></i>
            <i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <div class="label_btn" @click="innerLabel('h1')">
              <el-dropdown-item><h1>h1</h1></el-dropdown-item>
            </div>
            <div class="label_btn" @click="innerLabel('h2')">
              <el-dropdown-item><h2>h2</h2></el-dropdown-item>
            </div>
            <div class="label_btn" @click="innerLabel('h3')">
              <el-dropdown-item><h3>h3</h3></el-dropdown-item>
            </div>
            <div class="label_btn" @click="innerLabel('h4')">
              <el-dropdown-item><h4>h4</h4></el-dropdown-item>
            </div>
            <div class="label_btn" @click="innerLabel('h5')">
              <el-dropdown-item><h5>h5</h5></el-dropdown-item>
            </div>
          </el-dropdown-menu>
        </el-dropdown>
        <el-button type="button" @click="innerLabel('bold')">
          <i class="iconfont icon-cuti"></i>
        </el-button>
        <el-button type="button" @click="innerLabel('italic')">
          <i class="iconfont icon-xieti"></i>
        </el-button>
        <el-button type="button" @click="innerLabel('quote')">
          <i class="iconfont icon-baojiaquotation2"></i>
        </el-button>
        <el-button type="button" @click="innerLabel('hyperlink')">
          <i class="iconfont icon-link"></i>
        </el-button>
        <el-button type="button" @click="innerLabel('image')">
          <i class="iconfont icon-tupian"></i>
        </el-button>
        <el-button type="primary" style="float: right;margin: 10px;"
          @click="postArticle">Post</el-button>
        <el-button type="danger" style="float: right;margin: 10px;"
          @click="cancelArticle">Clear</el-button>
      </el-header>
      <el-container style="position: absolute;width: 100%;height: 79%;top: 21%;">
        <el-aside width="50%">
          <el-input
            type="textarea"
            class="userInput"
            id="userInput"
            placeholder="Start your creation"
            @input="userOnInput"
            v-model="userInput">
          </el-input>
        </el-aside>
        <el-aside  width="50%" style="right: 0px;border: 1px solid rgb(220, 223, 230);">
          <div class="markDown_box">
            <vue-markdown :source="markDown" class="markDown"></vue-markdown>
          </div>
        </el-aside>
      </el-container>
    </el-container>
    </div>
  </div>
</template>

<script>
import VueMarkdown from 'vue-markdown';
import Sc2 from 'sc2-sdk';
import Md5 from 'md5';

export default {
  name: 'index',
  data() {
    return {
      activeIndex: '1',
      userInfo: {},
      userInput: (window.localStorage.getItem('userInput') || ''),
      userName: '',
      markDown: '',
      title: (window.localStorage.getItem('title') || ''),
      tag: (window.localStorage.getItem('tag') || ''),
      insert: {
        h1: '\n# H1',
        h2: '\n## H2',
        h3: '\n### H3',
        h4: '\n#### H4',
        h5: '\n##### H5',
        bold: '\n**Bold**',
        italic: '\n*Italic*',
        quote: '\n>quote',
        hyperlink: '\n[text](hyperlink)',
        image: '\n![text](img_url)',
      },
      scroll: '',
      logStatus: false,
    };
  },
  components: {
    VueMarkdown,
  },
  methods: {
    handleSelect() {},
    userOnInput() {
      // this.consoleLog(this.userInput);
      window.localStorage.setItem('userInput', this.userInput);
      this.markDown = this.userInput;
    },
    handleClick() {},
    titleChange() {
      window.localStorage.setItem('title', this.title);
    },
    tagChange() {
      window.localStorage.setItem('tag', this.tag);
    },
    innerLabel(type) {
      this.consoleLog(type);
      this.addLabel(type);
    },
    addLabel(type) {
      const el = document.querySelector('.userInput textarea');
      const pos = this.getTextPosition(el);
      const insertText = this.innerText(pos);
      const index = pos.start + this.insert[type].length;
      this.consoleLog(pos);
      this.userInput = insertText.startPos + this.insert[type] + insertText.endPos;
      this.setCursorPosition(el, index);
      this.markDown = this.userInput;
    },
    formatUrl(address) {
      const text = address || '';
      const dateTmp = new Date();
      const month = dateTmp.getMonth() + 1;
      const day = dateTmp.getDate();
      const tmpYear = dateTmp.getFullYear();
      const tmpMonth = month > 9 ? month : `0${month}`;
      const tmpDay = day > 9 ? day : `0${day}`;
      const dateStr = `-${tmpYear}${tmpMonth}${tmpDay}`;
      let url = '';
      if (/[\u4e00-\u9fa5]+/.test(text)) {
        const re = text.replace(/[\u4e00-\u9fa5]+/g, '').toLowerCase() || '';
        url = (re ? re.replace(/ /g, '-') : Md5(text).slice(0, 6)) + dateStr;
      } else {
        url = text.replace(/ /g, '-').toLowerCase() + dateStr;
      }
      return url;
    },
    getCaretPosition() {},
    login() {
      Sc2.init({
        baseURL: 'https://v2.steemconnect.com',
        app: 'steemeditor',
        callbackURL: 'https://steemeditor.com',
        scope: ['vote', 'comment'],
      });
      const link = Sc2.getLoginURL();
      window.location.href = link;
    },
    logStatusChange() {
      if (!this.logStatus) {
        this.login();
      } else {
        this.logout();
      }
    },
    logout() {
      Sc2.revokeToken((err, result) => {
        window.localStorage.removeItem('userInput');
        window.localStorage.removeItem('title');
        window.localStorage.removeItem('tag');
        window.location.href = window.location.origin;
        // localStorage.removeItem('userInfo');
        this.consoleLog(result);
      });
    },
    cancelArticle() {
      this.userInput = '';
      this.markDown = '';
      window.localStorage.removeItem('userInput');
    },
    postArticle() {
      const tagList = this.tag.split(' ');
      const link = this.formatUrl(this.title);
      Sc2.comment('', tagList[0], this.userName, link, this.title, this.userInput, {
        tags: tagList,
        app: 'steemeditor/1.0.0',
      }, (err, res) => {
        window.localStorage.removeItem('userInput');
        window.localStorage.removeItem('title');
        window.localStorage.removeItem('tag');
        this.consoleLog(res);
      });
    },
    areaSceoll() {
      this.scroll = document.querySelector('.userInput textarea').scrollTop;
      document.querySelector('.markDown_box').scrollTop = this.scroll;
    },
    markSceoll() {
      this.scroll = document.querySelector('.markDown_box').scrollTop;
      document.querySelector('.userInput textarea').scrollTop = this.scroll;
    },
    setCursorPosition(elem, index) {
      this.consoleLog(index);
      const val = elem.value;
      const len = val.length;
      if (len < index) return;
      setTimeout(() => {
        elem.focus();
        if (elem.setSelectionRange) {
          elem.setSelectionRange(index, index);
        } else { // IE9-
          const range = elem.createTextRange();
          range.moveStart('character', -len);
          range.moveEnd('character', -len);
          range.moveStart('character', index);
          range.moveEnd('character', 0);
          range.select();
        }
      }, 10);
    },
    getTextPosition(el) {
      return (
        ('selectionStart' in el && function test() {
          const l = el.selectionEnd - el.selectionStart;
          return {
            start: el.selectionStart,
            end: el.selectionEnd,
            length: l,
            text: el.value.substr(el.selectionStart, l),
            base: el,
            select: l,
          };
        }) ||
        (document.selection && function test() {
          el.focus();
          const r = document.selection.createRange();
          const re = el.createTextRange();
          const rc = re.duplicate();
          if (r === null) {
            return {
              start: 0,
              end: el.value.length,
              length: 0,
            };
          }
          re.moveToBookmark(r.getBookmark());
          rc.setEndPoint('EndToStart', re);
          return {
            start: rc.text.length,
            end: rc.text.length + r.text.length,
            length: r.text.length,
            text: r.text,
            base: rc,
            select: r,
          };
        }) ||
        function test() {
          return null;
        }
      )();
    },
    innerText(pos) {
      const start = this.userInput.slice(0, pos.start);
      const end = this.userInput.slice(pos.end);
      return {
        startPos: start,
        endPos: end,
      };
    },
  },
  computed: {},
  mounted() {
    document.querySelector('.userInput textarea').addEventListener('scroll', this.areaSceoll);
    document.querySelector('.markDown_box').addEventListener('scroll', this.markSceoll);
    const param = {};
    if (param.access_token) {
      this.logStatus = true;
      Sc2.setAccessToken(param.access_token);
      Sc2.me((err, result) => {
        this.consoleLog(result);
        if (!err) {
          this.userInfo = result || {};
          this.consoleLog(this.userInfo);
          this.userName = this.userInfo.name;
        }
      });
    }
    this.markDown = this.userInput;
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  text-decoration: none;
}
.login_box {
    width: 200px;
    float: right;
    padding: 10px;
}

.el-header, .el-footer {
  /*background-color: #e4e4e4;*/
  color: #333;
  text-align: left;
  line-height: 60px;
}

.el-aside {
  /*background-color: #D3DCE6;*/
  color: #333;
  text-align: initial;
  /*text-align: center;*/
  min-height: 80%;
}

.markDown_box {
  overflow: scroll;
  height: 100%;
}

/*.el-main {
  background-color: #E9EEF3;
  color: #333;
  text-align: center;
  line-height: 400px;
}*/

body > .el-container {
  margin-bottom: 40px;
}

.el-container:nth-child(5) .el-aside,
.el-container:nth-child(6) .el-aside {
  line-height: 260px;
}

.el-container:nth-child(7) .el-aside {
  line-height: 320px;
}

.body_box {
  margin-top: 20px;
  width: 90%;
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  -webkit-transform: translateX(-50%);
  -moz-transform: translateX(-50%);
  -ms-transform: translateX(-50%);
  -o-transform: translateX(-50%);
  height: 90%;
}

.userInput {
  height: 100%;
}

.markDown {
  min-height: 97%;
  width: 97%;
  background-color: #fff;
  /*border: 1px solid #e1e3e9;*/
  padding: 0px 10px 2px;
}

.el-dropdown {
  vertical-align: top;
}

.el-dropdown + .el-dropdown {
  margin-left: 15px;
}

.el-icon-arrow-down {
  font-size: 12px;
}

.el-dropdown-selfdefine {
  margin: 8px;
}

.text_info {
  margin-top: 10px;
}
.text_info .el-input {
  display: block;
  margin-bottom: 10px;
}
.logo_box {
    width: 200px;
    float: left;
    line-height: 60px;
    font-size: 25px;
    background-image: url(/static/steem-editor.png);
    background-size: 50px 40px;
    background-repeat: no-repeat;
    background-position: center left;
    padding-left: 25px;
}
</style>
