<template>
  <!--
          作者：luoyiming
          时间：2019-10-24
          描述：后台系统头部
      -->
  <div class="common-header">
    <div class="breadcrumb">
      <div class="baseInfo-left-base">
        <span class="name">{{ menu_title }}</span>
      </div>
      <div class="header-navbar">
        <div class="header-navbar-icon">
          <span class="ml4 icon iconfont icon-geren9"></span>
          <span class="text ml4">{{ username }}，欢迎您！</span>
        </div>
        <div class="header-navbar-icon" @click="login_out()">
          <span class="icon iconfont icon-tuichu"></span>
          <span class="text ml4">退出</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import UserApi from '@/api/user.js';
import bus from '@/utils/eventBus';
import { delCookie, getCookie } from '@/utils/base.js';
export default {
  data() {
    return {
      menu_title: '首页',
       username:''
    };
  },
  created() {
    bus.$on('MenuName', res => {
      this.menu_title = res;
    });
    
    /*获取登录用户名*/
    this.username= getCookie('userinfo');
  },
  methods: {
    /*退出登录*/
    login_out() {
      this.$confirm('此操作将退出登录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          UserApi.loginOut(true)
            .then(data => {
              delCookie('isLogin');
              this.$router.push({
                path: '/Login'
              });
            })
            .catch(error => {});
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: '已取消退出'
          });
        });
    }
  }
};
</script>
<style lang="scss">
	.login-out .icon-tuichu {
	  color: red;
	}
	
	.header-navbar-icon .icon-geren9 {
	  font-size: 20px;
	}
</style>
