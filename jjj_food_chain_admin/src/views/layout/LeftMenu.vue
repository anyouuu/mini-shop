<template>
  <!--
    	作者：luoyiming
    	时间：2019-10-24
    	描述：后台系统左侧菜单
    -->
  <div class="left-menu-wrapper">
    <!--主菜单-->
    <div class="menu-wrapper">
      <div class="home-login">
        <div :class="active_menu != null ? 'home-icon' : 'home-icon router-link-active'" @click="choseMenu(null)">
          <span class="icon iconfont icon-tubiaozhizuomoban-"></span>
        </div>
      </div>
      <div class="nav-wrapper">
        <div class="first-menu-content">
          <ul class="nav-ul">
            <li :class="active_menu == index ? 'menu-item router-link-active' : 'menu-item'" v-for="(item, index) in menuList" :key="index" @click="choseMenu(item)">
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
          >
            <span class="name">{{ item.name }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import bus from '@/utils/eventBus';
export default {
  components: {},
  data() {
    return {
      /*选中的菜单*/
      active_menu: null,
      /*子菜单选择*/
      active_child: 0,
      /*菜单数据*/
      menuList: [
        {
          name: '后台权限',
          icon: 'icon-authority',
          path: '/access',
          children: [
            {
              name: '权限列表',
              icon: null,
              path: '/access/Index'
            }
          ]
        },
        {
          name: '商城',
          icon: 'icon-zhuye',
          path: '/shop',
          children: [
            {
              name: '商城列表',
              icon: null,
              path: '/shop/Index'
            }
          ]
        },
        {
          name: '消息',
          icon: 'icon-xiaoxi1',
          path: '/message',
          children: [
            {
              name: '消息列表',
              icon: null,
              path: '/message/Index'
            }
          ]
        },
        {
          name: '设置',
          icon: 'icon-icon-test1',
          path: '/setting',
          children: [
            {
              name: '设置',
              icon: null,
              path: '/setting/service/index'
            }
          ]
        },
        {
          name: '密码',
          icon: 'icon-authority1',
          path: '/password',
          children: [
            {
              name: '修改密码',
              icon: null,
              path: '/password/Index'
            }
          ]
        }
      ]
    };
  },
  created() {
    /*页面加载判断哪个菜单*/
    this.selectMenu(this.$route);
  },
  watch: {
    //监听路由
    $route(to, from) {
      const toDepth = to.path.split('/').length;
      const fromDepth = from.path.split('/').length;
      this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left';
      this.selectMenu(to);
    }
  },
  methods: {
    /*菜单*/
    selectMenu(to) {
      let menu_name = '首页';
      let menupath = '/' + to.path.split('/')[1];
      for (let i = 0; i < this.menuList.length; i++) {
        /*判断主菜单选择*/
        if (menupath == this.menuList[i]['path']) {
          menu_name = this.menuList[i]['name'];
          this.active_menu = i;
          /*判断子菜单选择*/
          let path = to.path;
          if (this.menuList[i]['children']) {
            let childs = this.menuList[i]['children'];
            for (let c = 0; c < childs.length; c++) {
              if (path == childs[c]['path']) {
                menu_name = childs[c]['name'];
                this.active_child = c;
                break;
              } else {
                this.active_child = 0;
              }
            }
          }
          break;
        } else {
          this.active_menu = null;
        }
      }
      this.$emit('selectMenu', this.active_menu);
      bus.$emit('MenuName', menu_name);
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
