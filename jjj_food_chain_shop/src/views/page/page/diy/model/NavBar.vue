<template>
  <!--
      	描述：diy组件-模拟显示-导航组
      -->
  <div @click.stop="$parent.$parent.onEditer(index)">
    <div class="drag optional" :class="{selected:index === selectedIndex}">
      <div class="diy-navBar">
        <ul class="list" :class="'column-'+item.style.rowsNum" v-if="item.style.rowsNum==1"  :style="{background:item.style.background}">
          <li class="item" :key="index" v-for="(navBar,index) in item.data">
            <div class="d-c d-s-s">
              <div class="item-text1 text-ellipsis">
                {{navBar.title}}
              </div>
              <div class="item-text2 text-ellipsis" :style="{color:navBar.color}">
                {{navBar.text}}
              </div>
            </div>
            <div class="item-navimg">
               <img v-img-url="navBar.imageUrl" alt="">
            </div>
          </li>
        </ul>
        <ul class="list" :class="'column-'+item.style.rowsNum" v-if="item.style.rowsNum==2"  :style="{background:item.style.background}">
          <li class="item" :key="index" v-for="(navBar,index) in item.data">
            <div class="item-navimg">
              <img v-img-url="navBar.imageUrl" alt="">
            </div>
            <div class="item-text1 text-ellipsis tc">
              {{navBar.title}}
            </div>
            <div class="item-text2 text-ellipsis tc"  :style="{color:navBar.color}">
              {{navBar.text}}
            </div>
          </li>
        </ul>
      </div>
      <div class="btn-edit-del">
        <div class="btn-del" @click.stop="$parent.$parent.onDeleleItem(index)">删除</div>
      </div>
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
  .diy-navBar .list {
    padding: 10px;
    box-sizing: border-box;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
  }

  .diy-navBar .list .item {
    padding: 10px;
    display: flex;

    background: #FFFFFF;
    box-shadow: 0px 2px 3px rgba(0, 0, 0, 0.16);
    border-radius: 9px;
    box-sizing: border-box;
  }

  .diy-navBar .list.column-1 .item {
    width: 345px;
    height: 75px;
    margin: 0 auto;
    justify-content: space-between;
    align-items: center;
  }

  .diy-navBar .list.column-2 .item {
    width: 165px;
    height: 125px;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    .item-navimg{
      margin-bottom: 6px;
    }
  }

  .diy-navBar .list .item-image {
    width: 60%;
  }

  .diy-navBar .list .item-image img {
    width: 100%;
  }

  .diy-navBar .list .item-text1 {
    width: 100%;
    font-size: 15px;
    font-weight: 600;
    color: #333333;
  }
  .diy-navBar .list .item-text2 {
    width: 100%;
    padding: 4px 0;
    font-weight: 400;
    color: #999999;
    font-size: 11px;
  }
  .item-navimg{
    width: 50px;
    height: 50px;
    img{
      width: 50px;
      height: 50px;
    }
  }
</style>
