<template>
  <!--
    	作者：luoyiming
    	时间：2019-10-24
    	描述：后台母版
    -->
  <div :class="hasChild != null ? 'main' : 'main right-big'">
    <!--left menu-->
    <LeftMenu @selectMenu="selectMenuFunc"></LeftMenu>

    <!--right content-->
    <RightContent></RightContent>
  </div>
</template>

<script>
  import LeftMenu from '@/views/layout/LeftMenu.vue';
  import RightContent from '@/views/layout/RightContent.vue';
  import store from '@/store/';
  import {
    delCookie
  } from '@/utils/base.js';
  export default {
    components: {
      /*左菜单组件*/
      LeftMenu,
      /*右边内容容器*/
      RightContent
    },
    data() {
      return {
        /*是否有子菜单*/
        hasChild: null,
        /*系统基本数据*/
        baseInfo: {
          shop_name: '',
          supplier_name:'',
          user: {
            user_type: null
          },
          version: ''
        }
      };
    },
    provide: function() {
      return {
        baseInfo: this.baseInfo
      };
    },
    created() {
      if (this.$route.query.from && this.$route.query.from == 'admin') {
        delCookie('baseInfo');
      }

      this.getBaseInof();
    },
    methods: {
      /*左边子组件传来的参数*/
      selectMenuFunc(param) {
        this.hasChild = param;
      },

      async getBaseInof() {
        let res = await store.dispatch('common/getBaseInfo');
        this.baseInfo.shop_name = res.shop_name;
        this.baseInfo.supplier_name = res.supplier_name;
        this.baseInfo.version = res.version;
        this.baseInfo.user = res.user;
      }
    }
  };
</script>

<style></style>
