<template>
  <div class="hello">
    <el-menu
    class="el-menu-demo"
    mode="horizontal"
    @select="handleSelect"
    text-color="#333"
    active-text-color="#1FBF8F"
    :default-active="activeIndex">
      <el-menu-item index="1">首页</el-menu-item>
      <div class="login_box">
        <span class="login_info">{{ userName }}</span>
        <el-button :type="(logStatus ? 'danger' : 'primary')"
        @click="logStatusChange">{{ logStatus ? '退出' : '登录' }}</el-button>
      </div>
    </el-menu>
    <div class="body_box">
      <div class="text_info">
        <el-input v-model="title" @change="titleChange" placeholder="请输入标题"></el-input>
        <el-input v-model="tag" @change="tagChange" placeholder="请输入标签"></el-input>
      </div>
      <el-container style="border: 1px solid #dcdfe6;">
      <el-header>
        <el-dropdown>
          <el-button type="button">
            <i class="iconfont icon-zihao"></i>
            <i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item><h1 @click="innerLabel('h1')">h1</h1></el-dropdown-item>
            <el-dropdown-item><h2 @click="innerLabel('h2')">h2</h2></el-dropdown-item>
            <el-dropdown-item><h3 @click="innerLabel('h3')">h3</h3></el-dropdown-item>
            <el-dropdown-item><h4 @click="innerLabel('h4')">h4</h4></el-dropdown-item>
            <el-dropdown-item><h5 @click="innerLabel('h5')">h5</h5></el-dropdown-item>
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
          @click="postArticle">提交</el-button>
        <el-button type="danger" style="float: right;margin: 10px;"
          @click="cancelArticle">清除</el-button>
      </el-header>
      <el-container style="position: absolute;width: 100%;height: 79%;top: 21%;">
        <el-aside width="50%">
          <el-input
            type="textarea"
            class="userInput"
            id="userInput"
            placeholder="请输入Markdown内容"
            @input="userOnInput"
            v-model="userInput">
          </el-input>
        </el-aside>
        <el-aside  width="50%" style="right: 0px;border: 1px solid rgb(220, 223, 230);">
          <vue-markdown :source="markDown" class="markDown"></vue-markdown>
        </el-aside>
      </el-container>
    </el-container>
    </div>
  </div>
</template>

<script>
import VueMarkdown from 'vue-markdown';

export default {
  name: 'Index',
  data() {
    return {
      activeIndex: '1',
      userInfo: {},
      userInput: (localStorage.getItem('userInput') || ''),
      userName: '',
      markDown: '',
      title: (localStorage.getItem('title') || ''),
      tag: (localStorage.getItem('tag') || ''),
      insert: {
        h1: '\n# H1',
        h2: '\n## H2',
        h3: '\n### H3',
        h4: '\n#### H4',
        h5: '\n##### H5',
        bold: '\n**Bold**',
        italic: '\n*Italic*',
        quote: '\n```quote```\n',
        hyperlink: '[Google]: http://google.com/',
        image: '![Alt text](http://wx2.sinaimg.cn/bmiddle/9d8ae485ly1fnoog4u9czg206a07ue89.gif "Optional title")',
      },
      sc2: window.sc2,
      md5: window.MD5,
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
      localStorage.setItem('userInput', this.userInput);
      this.markDown = this.userInput;
    },
    handleClick() {},
    titleChange() {
      localStorage.setItem('title', this.title);
    },
    tagChange() {
      localStorage.setItem('tag', this.tag);
    },
    innerLabel(type) {
      this.consoleLog(type);
      this.addLabel(type);
    },
    addLabel(type) {
      this.userInput = this.userInput + this.insert[type];
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
        url = (re ? re.replace(/ /g, '-') : this.md5(text).slice(0, 6)) + dateStr;
      } else {
        url = text.replace(/ /g, '-').toLowerCase() + dateStr;
      }
      return url;
    },
    getCaretPosition() {},
    login() {
      this.sc2.init({
        baseURL: 'https://v2.steemconnect.com',
        app: 'steemeditor',
        callbackURL: 'https://steemeditor.com',
        scope: ['vote', 'comment'],
      });
      const link = this.sc2.getLoginURL();
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
      this.sc2.revokeToken((err, result) => {
        localStorage.removeItem('userInput');
        localStorage.removeItem('title');
        localStorage.removeItem('tag');
        window.location.href = window.location.origin;
        // localStorage.removeItem('userInfo');
        this.consoleLog(result);
      });
    },
    cancelArticle() {
      this.userInput = '';
      this.markDown = '';
      localStorage.removeItem('userInput');
    },
    postArticle() {
      const tagList = this.tag.split(' ');
      const link = this.formatUrl(this.title);
      this.sc2.comment('', tagList[0], this.userName, link, this.title, this.userInput, {
        tags: tagList,
        app: 'steemeditor/1.0.0',
      }, (err, res) => {
        localStorage.removeItem('userInput');
        localStorage.removeItem('title');
        localStorage.removeItem('tag');
        this.consoleLog(res);
      });
    },
  },
  computed: {},
  mounted() {
    const param = this.getUrlParam().param;
    if (param.access_token) {
      this.logStatus = true;
      this.sc2.setAccessToken(param.access_token);
      this.sc2.me((err, result) => {
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
</style>
