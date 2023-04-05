<template>
  <!--
    	作者：luoyiming
    	时间：2019-10-24
    	描述：后台系统左侧菜单
    -->
  <div class="left-menu-wrapper" :class="baseInfo.user.user_type==1?'supplier':''">
    <!--主菜单-->
    <div class="menu-wrapper">
      <div class="home-login">
        <div :class="active_menu != null ? 'home-icon' : 'home-icon router-link-active'" @click="choseMenu(null)">
          <span class="icon iconfont icon-tubiaozhizuomoban-"></span>
        </div>
      </div>
      <div class="d-c-c">
        <span class="p-10-0 blue fb tc" v-if="baseInfo.user.user_type==1">{{baseInfo.supplier_name}}</span>
        <span class="p-10-0 blue fb tc"  v-if="baseInfo.user.user_type==0">{{baseInfo.shop_name}}</span>
      </div>
      <div class="nav-wrapper mt10">
        <div class="first-menu-content">
          <ul class="nav-ul">
            <li :class="active_menu == index ? 'menu-item router-link-active' : 'menu-item'" v-for="(item, index) in menuList" :key="index" @click="choseMenu(item)" v-if="item.is_menu==1">
              <div class="item-box">
                <span :class="'icon iconfont menu-item-icon ' + item.icon"></span>
                <span>{{ item.name }}</span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--子菜单-->
    <div class="child-menu-wrapper">
      <div class="child-menu right-animation">
        <ul v-if="active_menu != null">
          <li
            :class="active_child == index ? 'router-link router-link-active' : 'router-link'"
            v-for="(item, index) in menuList[active_menu]['children']"
            :key="index"
            @click="choseMenu(item)"
            v-if="item.is_menu==1"
          >
            <span class="name">{{ item.name }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import bus from '@/utils/eventBus.js';
import { getSessionStorage } from '@/utils/base';
import {isMenu} from '@/utils/isMenu.js'
export default {
  components: {},
  data() {
    return {
      /*传到顶部的标题*/
      munu_name: '首页',
      /*选中的菜单*/
      active_menu: null,
      /*子菜单选择*/
      active_child: 0,
      /*菜单数据*/
      menuList: [],
      /*商城名称*/
      shop_name:''
    };
  },
  inject: ['baseInfo'],
  created() {
    this.menuList = getSessionStorage('rolelist');
    /*监听传过来的值*/
    bus.$on('headLoad', res => {
      if (res) {
        bus.$emit('menuName', this.munu_name);
        /*页面加载判断哪个菜单*/
        this.selectMenu(this.$route);
      }
    });

  },
  watch: {
    //监听路由
    $route(to, from) {
      this.selectMenu(to);
    }
  },
  methods: {


    /*判断字符串第一个是否斜杠*/
    slantingBar(str) {
      str = str.toLowerCase();
      if (str.length > 0) {
        if (str.substr(0, 1) == '/') {
          return str;
        } else {
          return '/' + str;
        }
      } else {
        return str;
      }
    },

    /*菜单*/
    selectMenu(to) {
      this.munu_name = '首页';
      let menupath = to.path.toLowerCase();
      let active = null;
      for (let i = 0; i < this.menuList.length; i++) {
        let item = this.menuList[i];
        /*判断主菜单选择*/
        if (menupath == this.slantingBar(item['path']) || menupath == this.slantingBar(item['redirect_name'])) {
          this.munu_name = item['name'];
          this.active_child = 0;
          active = i;
          break;
        } else {
          /*判断子菜单选择*/
          if (item['children']) {
            let childs = item['children'];
            let child_index = null;
            for (let c = 0; c < childs.length; c++) {
              let child = childs[c];
              if (menupath == this.slantingBar(child['path'])) {
                active = i;
                this.munu_name = child['name'];
                this.active_child = c;
                break;
              } else {
                if (child['children']) {
                  let name = this.hasChild(menupath, child['children']);
                  if (name != null) {
                    active = i;
                    this.munu_name = name;
                    this.active_child = c;
                    break;
                  }
                }
              }
            }
          }
        }
      }
      this.active_menu = active;
      this.$emit('selectMenu', active);
      bus.$emit('menuName', this.munu_name);
    },

    /*判断子菜单有没有*/
    hasChild(path, list) {
      let name = null;
      for (let i = 0, length = list.length; i < length; i++) {
        let item = list[i];
        if (path == this.slantingBar(item['path'])) {
          name = item['name'];
          break;
        } else {
          if (item['children'] != null && item['children'].length > 0) {
            name = this.hasChild(path, item['children']);
            if (name != null) {
              break;
            }
          }
        }
      }
      return name;
    },

    /*点击菜单跳转*/
    choseMenu(item) {
      if (item != null) {
        let path = item.path;
        this.$router.push(path);
      } else {
        this.$router.push('/');
      }
    }
  }
};
</script>
<style>
  .home-login .icon-tubiaozhizuomoban-{
    color: #3a8ee6;
    font-size: 28px;
  }
  .menu-item-icon.icon.iconfont{
    font-size: 20px;
  }
  .menu-item .item-box{
    display: flex;
  }
</style>
