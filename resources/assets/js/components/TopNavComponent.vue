<template>
  <div class="top_nav">
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
  </div>
</template>
<script>
import axios from 'axios';

export default {
  name: 'topnav',
  data() {
    return {
      activeIndex: '1',
      userName: null,
      logStatus: false,
    };
  },
  methods: {
    handleSelect() {
    },
    login() {
      const link = this.api.getLoginURL();
      window.location.href = link;
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
<style></style>
