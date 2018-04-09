<template>
  <el-container>
    <el-main>
      <div id="post_info">
        <el-row>
          <el-input v-model="title" placeholder="Your Title"></el-input>
        </el-row>
        <el-row :gutter="10">
          <el-col :md="9" :lg="10" :xl="12">
            <el-input v-model="tags" placeholder="Tags (separate by space)"></el-input>
          </el-col>
          <el-col :md="9" :lg="10" :xl="10" style="text-align: right;">
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
          <el-col :md="6" :lg="4" :xl="2" style="text-align: right;">
            <el-button type="primary" @click="toPost">Post</el-button>
            <el-button type="danger" @click="clearPost">Clear</el-button>
          </el-col>
        </el-row>
      </div>
      <editor
        v-if="editorConfig !== null"
        ref="mainEditor"
        :init-content="content"
        :insert-content="insertContent"
        :clear-content="clear"
        @clearInsertContent="handleClearInsertContent"
        :editor-config="editorConfig"
        @contentChange="updateContent"
        @resetClear="handleResetClear">
      </editor>
    </el-main>
    <el-dialog
      ref="dialog__wrapper"
      v-dialogDrag
      width="30%"
      title="Material"
      :modal="false"
      :visible.sync="dialogVisible">
      <material-list
        v-dialogDragWidth="$refs.dialog__wrapper"
        :all-public="true"
        :height="asideHeight"
        :refresh="false"
        @addMaterialMsg="handleAddMaterialMsg">
      </material-list>
    </el-dialog>
  </el-container>
</template>

<script>
import base58 from 'bs58';
import secureRandom from 'secure-random';
import getSlug from 'speakingurl';
import steem from 'steem';
import Editor from './EditorComponent.vue';
import MaterialList from './Material/ListComponent.vue';

export default {
  name: 'index',
  data() {
    return {
      userInfo: {},
      content: null,
      title: null,
      tags: null,
      logStatus: false,
      sc: window.sc,
      reward: 50,
      asideHeight: this.getAsideHeight(),
      dialogVisible: false,
      insertContent: null,
      clear: false,
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
      username: null,
      editorConfig: null,
    };
  },
  components: {
    Editor,
    MaterialList,
  },
  mounted() {
    const refs = this.$refs;
    const that = this;
    this.editorConfig = {
      width: '100%',
      height: this.getEditorHeight(),
      path: '/plugins/editor/lib/',
      codeFold: true,
      saveHTMLToTextarea: true,
      watch: true,
      searchReplace: true,
      htmlDecode : "style,script,iframe|on*",
      placeholder: 'Start your creation !!!',
      previewCodeHighlight: false,
      emoji: true,
      toolbarIcons: () => [
        'material', '|',
        'undo', 'redo', '|',
        'bold', 'del', 'italic', 'quote', 'ucwords', 'uppercase', 'lowercase', '|',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '|',
        'list-ul', 'list-ol', 'hr', '|',
        'link', 'reference-link', 'image', 'code', 'code-block', 'table', '|',
        'goto-line', 'clear', 'search', 'watch', 'fullscreen',
      ],
      toolbarIconTexts: {
        material: 'Material',
      },
      toolbarHandlers: {
        material(cm, icon, cursor, selection) {
          if (that.dialogVisible === false) {
            that.dialogVisible = true;
          } else {
            that.dialogVisible = false;
          }
          window.consoleLog(['toolbar in index', cm, icon, cursor, selection]);
        },
      },
      imageUpload: true,
      imageFormats: ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'],
      imageUploadURL: '/api/file/upload',
      onload: () => {
        window.consoleLog(['onload in indexcomponent config']);
      },
      onchange() {
        refs.mainEditor.$emit('contentChange', this.markdownTextarea[0].innerHTML);
      },
    };
    this.$nextTick(() => {
      this.logStatus = this.$store.getters.logStatus;
      this.userInfo = this.$store.getters.userInfo;
      this.username = this.logStatus ? this.userInfo.name : null;
      this.title = this.$store.getters.title;
      this.content = this.$store.getters.content;
      this.tags = this.$store.getters.tags;
      window.consoleLog(['index component mounted', this.logStatus, this.userInfo, this.content]);
    });
  },
  watch: {
    title(val) {
      this.$store.commit('title', val);
    },
    tags(val) {
      this.$store.commit('tags', val);
    },
  },
  methods: {
    getAsideHeight() {
      // const totalHeight = window.innerHeight;
      // return `${String(totalHeight - 480)}px`;
      return '400px';
    },
    getEditorHeight() {
      const totalHeight = window.innerHeight;
      return String(totalHeight - 60 - 150 - 60);
    },
    handleAddMaterialMsg(data) {
      window.consoleLog(['material component handleAddMaterialMsg', data]);
      this.insertContent = data.body;
    },
    handleClearInsertContent() {
      window.consoleLog(['clear insert content in index component']);
      this.insertContent = null;
    },
    handleResetClear() {
      window.consoleLog(['clear editor content']);
      this.clear = false;
    },
    updateContent(data) {
      window.consoleLog(['content update', data]);
      this.$store.commit('content', data);
      this.content = data;
    },
    clearPost() {
      window.consoleLog(['clear post']);
      this.$store.commit('title', null);
      this.$store.commit('tags', null);
      this.$store.commit('content', null);
      this.title = null;
      this.tags = null;
      this.content = null;
      this.clear = true;
    },
    toPost() {
      const msg = 'With each article posted by steeemeditor, steeemeditor will extract 5% of the article’s revenue as bonus.';
      this.$confirm(
        msg,
        'Notify',
        {
          confirmButtonText: 'Continue',
          cancelButtonText: 'Cancel',
          type: 'warning',
        },
      )
        .then(() => {
          this.postArticle();
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: 'Post has been cancled.',
          });
        });
    },
    async postArticle() {
      window.consoleLog(['post article', this.logStatus, this.title, this.tags, this.content]);
      if (this.logStatus === true) {
        if (this.title && this.tags && this.content) {
          const tagList = this.tags.split(' ');
          const link = await this.createPermlink(this.title, this.username, '', tagList[0]);
          window.consoleLog([link, 'post_link']);
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
              author: this.username,
              permlink: link,
              title: this.title,
              body: this.content,
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
                  author: this.username,
                  permlink: link,
                  max_accepted_payout: '1000000.000 SBD',
                  percent_steem_dollars: 0,
                  allow_votes: true,
                  allow_curation_rewards: true,
                  extensions: [
                    [
                      0,
                      {
                        beneficiaries: [
                          {
                            account: 'steemeditor.bot',
                            weight: 500,
                          },
                        ],
                      },
                    ],
                  ],
                },
              ];

              break;
            case 50:
              commentOptions = [
                'comment_options',
                {
                  author: this.username,
                  permlink: link,
                  max_accepted_payout: '1000000.000 SBD',
                  percent_steem_dollars: 10000,
                  allow_votes: true,
                  allow_curation_rewards: true,
                  extensions: [
                    [
                      0,
                      {
                        beneficiaries: [
                          {
                            account: 'steemeditor.bot',
                            weight: 500,
                          },
                        ],
                      },
                    ],
                  ],
                },
              ];
              break;
            case 0:
              commentOptions = [
                'comment_options',
                {
                  author: this.username,
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
              voter: this.username,
              author: this.username,
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
          this.sc.broadcast(operations)
            .then((res) => {
              this.$store.commit('content');
              this.$store.commit('title');
              this.$store.commit('tags');
              window.consoleLog([res, 'postArticle then']);
              this.$notify({
                title: 'Success',
                message: 'Post successfully',
                type: 'success',
              });
              this.clearPost();
            })
            .catch((err) => {
              switch (err.error_description) {
                case 'body.size() > 0: Body is empty':
                  this.$notify.error({
                    title: 'Warning',
                    message: 'Body is empty.',
                  });
                  break;
                default:
                  this.$notify.error({
                    title: 'Unknown Error',
                    message: err.error_description,
                  });
                  break;
              }
              window.consoleLog([err, 'postArticle catch'], 'msg');
            });
        } else {
          window.consoleLog(['post error', typeof this.content, this.content]);
          const msg = `Title: ${this.title}\nTags: ${this.tags}\nContent Length: ${this.content.length}`;
          this.$notify({
            title: 'Warning',
            message: `Title or tags or content is empty.\n${msg}`,
            type: 'warning',
          });
        }
      } else {
        this.$notify({
          title: 'Warning',
          message: 'Please login first',
          type: 'warning',
        });
      }
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
  },
  computed: {},
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.el-row {
  margin: 20px 0;
}
</style>
