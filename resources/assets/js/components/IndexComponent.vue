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
        <span class="site_title">Steem Editor</span>
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
        <el-row>
          <el-col :span="12">
            <el-input v-model="tag" @change="tagChange" placeholder="Tags (separate by space)"></el-input>
          </el-col>
          <el-col :span="12">
            <el-select v-model="reward" placeholder="Reward">
              <el-option
                v-for="item in rewardOptions"
                :key="item.value"
                :label="item.text"
                :value="item.value">
              </el-option>
            </el-select>
            <el-checkbox v-model="isUpvote" label="Upvote post" border></el-checkbox>
          </el-col>
        </el-row>
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
import base58 from 'bs58';
import secureRandom from 'secure-random';
import getSlug from 'speakingurl';
import steem from 'steem';
import axios from 'axios';

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
      api: null,
      reward: 50,
      rewardOptions: [
        {
          value: 100,
          text: 'Power Up 100%',
        },
        {
          value: 50,
          text: 'Default (50% / 50%)',
        },
        {
          value: 0,
          text: 'Decline Payout',
        },
      ],
      isUpvote: true,
    };
  },
  components: {
    VueMarkdown,
  },
  methods: {
    handleSelect() {},
    userOnInput() {
      // window.consoleLog([this.userInput]);
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
      window.consoleLog([type, 'innerLabel']);
      this.addLabel(type);
    },
    addLabel(type) {
      const el = document.querySelector('.userInput textarea');
      const pos = this.getTextPosition(el);
      const insertText = this.innerText(pos);
      const index = pos.start + this.insert[type].length;
      window.consoleLog([pos, 'addLabel']);
      this.userInput = insertText.startPos + this.insert[type] + insertText.endPos;
      this.setCursorPosition(el, index);
      this.markDown = this.userInput;
    },
    getCaretPosition() {},
    login() {
      const link = this.api.getLoginURL();
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
      this.api.revokeToken((logoutErr, result) => {
        window.localStorage.removeItem('userInput');
        window.localStorage.removeItem('title');
        window.localStorage.removeItem('tag');
        axios.post('/logout', { accessToken: window.Laravel.accessToken })
          .then(() => {
            this.$message = 'Logout success!';
            window.location.href = window.location.origin;
            window.consoleLog([result, 'logout']);
          })
          .catch((err) => {
            window.consoleLog([err, 'logout err']);
          });
      });
    },
    cancelArticle() {
      this.userInput = '';
      this.markDown = '';
      window.localStorage.removeItem('userInput');
    },
    async postArticle() {
      const tagList = this.tag.split(' ');
      const link = await this.createPermlink(this.title, this.userName, '', tagList[0]);
      window.consoleLog([link, 'postArticle123']);
      // json metadata
      const jsonMetadata = {
        tags: tagList,
        app: 'steemeditor/0.1.0',
        format: 'markdown',
        app_url: 'https://steemeditor.com',
      };

      let comment = null;
      let commentOptions = null;
      let vote = null;
      const operations = [];

      // comment
      comment = [
        'comment',
        {
          parent_author: '',
          parent_permlink: tagList[0],
          author: this.userName,
          permlink: link,
          title: this.title,
          body: this.userInput,
          json_metadata: window.JSON.stringify(jsonMetadata),
        },
      ];
      operations.push(comment);

      // reward
      switch (this.reward) {
        case 100:
          commentOptions = [
            'comment_options',
            {
              author: this.userName,
              permlink: link,
              max_accepted_payout: '1000000.000 SBD',
              percent_steem_dollars: 0,
              allow_votes: true,
              allow_curation_rewards: true,
              extensions: [],
            },
          ];

          break;
        case 50:
          commentOptions = null;
          break;
        case 0:
          commentOptions = [
            'comment_options',
            {
              author: this.userName,
              permlink: link,
              max_accepted_payout: '0.000 SBD',
              percent_steem_dollars: 10000,
              allow_votes: true,
              allow_curation_rewards: true,
              extensions: [],
            },
          ];
          break;
        default:
          window.consoleLog(['err_commentOptions'], 'msg');
          return;
      }

      window.consoleLog([commentOptions, 'commentOptions']);
      if (commentOptions !== null) {
        operations.push(commentOptions);
      }

      // vote
      vote = [
        'vote',
        {
          voter: this.userName,
          author: this.userName,
          permlink: link,
          weight: 10000,
        },
      ];
      operations.push(vote);

      // post comment
      this.$notify.info({
        title: 'Please wait',
        message: 'Sending data.',
      });
      this.api.broadcast(operations)
        .then((res) => {
          window.localStorage.removeItem('userInput');
          window.localStorage.removeItem('title');
          window.localStorage.removeItem('tag');
          window.consoleLog([res, 'postArticle then']);
          this.$notify.info({
            title: 'Success',
            message: 'Post successfully',
            type: 'success',
          });
        })
        .catch((err) => {
          switch (err.error_description) {
            case 'body.size() > 0: Body is empty':
              this.$notify.info({
                title: 'Warning',
                message: 'Body is empty.',
                type: 'warning',
              });
              break;
            default:
              break;
          }
          window.consoleLog([err, 'postArticle catch'], 'msg');
        });
    },
    async createPermlink(title, author, parentAuthor, parentPermlink) {
      let permlink;
      if (title && title.trim() !== '') {
        let s = this.slug(title);
        if (s === '') {
          s = base58.encode(secureRandom.randomBuffer(4));
        }
        // ensure the permlink(slug) is unique
        const slugState = await steem.api.getContentAsync(author, s);
        window.consoleLog([slugState.body, 'slugState']);
        let prefix;
        if (slugState.body !== '') {
          // make sure slug is unique
          prefix = `${base58.encode(secureRandom.randomBuffer(4))}-`;
        } else {
          prefix = '';
        }
        permlink = prefix + s;
        window.consoleLog([permlink, 'then']);
      } else {
        // comments: re-parentauthor-parentpermlink-time
        const timeStr = new Date().toISOString().replace(/[^a-zA-Z0-9]+/g, '');
        const tmpParentPermlink = parentPermlink.replace(/(-\d{8}t\d{9}z)/g, '');
        permlink = `re-${parentAuthor}-${tmpParentPermlink}-${timeStr}`;
      }
      window.consoleLog([permlink, 'createPermlink']);
      if (permlink.length > 255) {
        // STEEMIT_MAX_PERMLINK_LENGTH
        permlink = permlink.substring(permlink.length - 255, permlink.length);
      }
      // only letters numbers and dashes shall survive
      permlink = permlink.toLowerCase().replace(/[^a-z0-9-]+/g, '');
      return permlink;
    },
    slug(text) {
      return getSlug(text.replace(/[<>]/g, ''), { truncate: 128 });
    },
    areaScoll() {
      this.scroll = document.querySelector('.userInput textarea').scrollTop;
      document.querySelector('.markDown_box').scrollTop = this.scroll;
    },
    markScoll() {
      this.scroll = document.querySelector('.markDown_box').scrollTop;
      document.querySelector('.userInput textarea').scrollTop = this.scroll;
    },
    setCursorPosition(elem, index) {
      window.consoleLog([index, 'setCursorPosition']);
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
    this.api = Sc2.Initialize(window.Laravel.sc2);
    document.querySelector('.userInput textarea').addEventListener('scroll', this.areaScoll);
    document.querySelector('.markDown_box').addEventListener('scroll', this.markScoll);
    if (window.Laravel.accessToken) {
      this.logStatus = true;
      this.api.setAccessToken(window.Laravel.accessToken);
      this.userInfo = window.Laravel.userInfo || {};
      window.consoleLog([this.userInfo, 'mounted']);
      this.userName = this.userInfo.name;
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
