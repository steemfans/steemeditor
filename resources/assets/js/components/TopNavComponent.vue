<template>
  <el-header>
    <el-row>
      <el-col :span="4">
        <h1>SteemEditor</h1>
      </el-col>
      <el-col :span="14">
        <el-menu
          :default-active="activeIndex"
          class=""
          mode="horizontal"
          :router=true>
          <el-menu-item index="/">Editor</el-menu-item>
          <el-menu-item index="/material">Material Manager</el-menu-item>
          <!--<el-menu-item index="material">Material Manager</el-menu-item>
          <el-menu-item index="4">
            <a href="#" target="_blank"></a>
          </el-menu-item>-->
        </el-menu>
      </el-col>
      <el-col :span="6" style="padding: 10px 0;text-align: right;">
        {{ username }}
        <el-button :type="(logStatus ? 'danger' : 'primary')"
        @click="logStatusChange">{{ logStatus ? 'Logout' : 'Login' }}</el-button>
      </el-col>
    </el-row>
  </el-header>
</template>
<script>
import axios from 'axios';

export default {
  name: 'topnav',
  data() {
    return {
      activeIndex: this.$route.path,
      userInfo: {},
      username: null,
      logStatus: false,
      sc: window.sc,
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.userInfo = this.$store.getters.userInfo;
      this.logStatus = this.$store.getters.logStatus;
      this.username = this.logStatus ? this.userInfo.name : null;
      window.consoleLog(['status', this.userInfo, this.logStatus, this.username]);
    });
  },
  methods: {
    login() {
      const link = this.sc.getLoginURL();
      window.location.href = link;
    },
    logout() {
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)',
      });

      this.sc.revokeToken((logoutErr, result) => {
        window.localStorage.removeItem('userInput');
        window.localStorage.removeItem('title');
        window.localStorage.removeItem('tag');
        axios.post('/logout', { accessToken: window.Laravel.accessToken })
          .then(() => {
            loading.close();
            this.$store.commit('logStatus', false);
            this.$store.commit('userInfo', {});
            this.$store.commit('content', null);
            this.$store.commit('title', null);
            this.$store.commit('tags', null);
            this.$message = 'Logout success!';
            window.location.href = window.location.origin;
            window.consoleLog([result, 'logout']);
          })
          .catch((err) => {
            window.consoleLog([err, 'logout err']);
          });
      });
    },
    logStatusChange() {
      if (!this.logStatus) {
        this.login();
      } else {
        this.logout();
      }
    },
  },
};
</script>
<style>
.el-menu--horizontal {
  border-bottom: 0 !important;
}
</style>
