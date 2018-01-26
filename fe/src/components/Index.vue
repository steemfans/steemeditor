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
        <el-button type="primary" @click="logout">{{ userName ? '退出' : '登录' }}</el-button>
      </div>
    </el-menu>
    <div class="body_box">
      <el-container>
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
      </el-header>
      <el-container>
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
        <el-aside  width="50%">
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
      userInput: '### 这里是要展示的markdown文字，也可以通过props传递',
      userName: '',
      markDown: '',
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
    };
  },
  components: {
    VueMarkdown // 声明组件
  },
  methods: {
    handleSelect() {},
    userOnInput() {
      console.log(this.userInput);
      this.markDown = this.userInput;
    },
    handleClick() {
      alert('button click');
    },
    innerLabel(type) {
      console.log(type);
      this.addLabel(type);
    },
    addLabel(type) {
      this.userInput = this.userInput + this.insert[type];
      this.markDown = this.userInput;
    },
    getCaretPosition(oField) {},
    login() {
      alert(1)
    },
    logout() {
      sc2.revokeToken(function (err, result) {
        console.log('You successfully logged out', err, result);
      });
    },
  },
  computed: {},
  mounted () {
    this.userInfo = JSON.parse(localStorage.getItem('userInfo'));
    this.userName = this.userInfo.name;
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
  min-height: 400px;
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
  margin: 0 auto;
}

.userInput {
  height: 100%;
}

.markDown {
  height: 99%;
  width: 95%;
  background-color: #fff;
  border: 1px solid #e1e3e9;
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
</style>
