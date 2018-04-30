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
          <el-menu-item index="/voteme">Vote ME!!!</el-menu-item>
        </el-menu>
      </el-col>
      <el-col :span="6" style="padding: 10px 0;text-align: right;">
        <el-dropdown v-if="username">
          <span class="el-dropdown-link">
            {{ username }}<i class="el-icon-arrow-down el-icon--right"></i>
          </span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item>
              <a :href="`https://steemit.com/@${username}`" target="_blank">Go to your steemit page</a>
            </el-dropdown-item>
            <el-dropdown-item>
              <a :href="`https://cnsteem.com/@${username}`" target="_blank">Go to your cnsteem page</a>
            </el-dropdown-item>
            <el-dropdown-item>
              <a :href="`https://busy.org/@${username}`" target="_blank">Go to your busy.org page</a>
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
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
        axios.post('/logout', { accessToken: window.Laravel.accessToken })
          .then(() => {
            loading.close();
            this.$store.commit('logStatus', false);
            this.$store.commit('userInfo', {});
            this.$store.commit('content', null);
            this.$store.commit('title', null);
            this.$store.commit('tags', null);
            this.$message = 'Logout success!';
            window.consoleLog(['logout', result]);
            window.location.href = window.location.origin;
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
