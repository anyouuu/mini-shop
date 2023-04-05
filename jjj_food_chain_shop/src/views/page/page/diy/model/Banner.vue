<template>
  <!--
    	作者：luoyiming
    	时间：2020-06-20
    	描述：diy组件-模拟显示-轮播图
    -->
  <div class="drag optional" @click.stop="$parent.$parent.onEditer(index)" :class="{selected: index === selectedIndex}">
    <div class="diy-banner">
      <div class="img-list">
        <img :key="index" v-for="(banner, index) in item.data" v-if="index <= 1" v-img-url="banner.imgUrl">
      </div>
      <div class="dots center" :class="item.style.btnShape">
        <span :key="index" v-for="(banner,index) in item.data" :style="{background:item.style.btnColor}"></span>
      </div>
    </div>
    <div class="diy-banner-xf d-b-c">
      <div class="diy-banner-box">
        <div class="rides">
          <img :src="item.params.nav[0].navimgUrl">
        </div>
        <div class="rides_title">{{item.params.nav[0].title}}</div>
        <div class="rides_text">{{item.params.nav[0].text}}</div>
      </div>
      <div class="diy-banner-box">
        <div class="rides">
          <img :src="item.params.nav[1].navimgUrl">
        </div>
        <div class="rides_title">{{item.params.nav[1].title}}</div>
        <div class="rides_text">{{item.params.nav[1].text}}</div>
      </div>
    </div>
    <div class="btn-edit-del">
      <div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div>
    </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {

      };
    },
    props: ['item', 'index', 'selectedIndex'],
    methods: {

    }
  };
</script>

<style lang="scss">
  .diy-banner {
    height: 180px;
    overflow: hidden;
  }

  .diy-banner img {
    width: 100%;
    height: 180px;
    object-fit: fill;
  }

  .diy-banner-box {
    flex: 1;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
  }

  .diy-banner-xf {
    width: 345px;
    height: 192px;
    margin-top: -40px;
    background: #FFFFFF;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.16);
    opacity: 1;
    border-radius: 15px;
    margin: 0 auto;
    position: relative;
    margin-top: -40px;
  }

  .rides {
    width: 60px;
    height: 60px;
    margin-bottom: 14px;
    img {
      width: 100%;
      height: 100%;
    }
  }
  .rides_title{
    color: #333333;
    font-weight: 600;
    font-size: 20px;

  }
  .rides_text{
     color: #999999;
     font-weight: 400;
     font-size: 14px;
  }
</style>
