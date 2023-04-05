<template>
  <!--
    	作者：luoyiming
    	时间：2019-10-24
    	描述：后台母版
    -->
  <div :class="hasChild!=null?'main right-big':'main right-big'">

    <!--left menu-->
    <LeftMenu @selectMenu='selectMenuFunc'></LeftMenu>

    <!--right content-->
    <RightContent></RightContent>

  </div>
</template>

<script>
import LeftMenu from '@/views/layout/LeftMenu.vue'
import RightContent from '@/views/layout/RightContent.vue'

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
      hasChild:null
    };
  },
  created() {},
  methods: {
    /*左边子组件传来的参数*/
    selectMenuFunc(param){
      this.hasChild=param;
    }

  }
};
</script>

<style></style>
