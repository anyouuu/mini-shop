<template>
  <!--
      	描述：商品-店内商品
      -->
  <div class="common-seach-wrap">
    <!--商品管理-->
    <product v-if="activeName=='product'"></product>
    <!--分类管理-->
    <category v-if="activeName=='category'"></category>
  </div>
</template>
<script>
  import bus from '@/utils/eventBus.js';
  import category from './category/index.vue';
  import product from './product/index.vue';
  export default {
    components: {
      category,
      product,
    },
    data() {
      return {
        /*是否加载完成*/
        loading: true,
        form: {},
        /*参数*/
        param: {},
        /*当前选中*/
        activeName: 'product',
        /*切换数组原始数据*/
      sourceList: [
        {
            key: 'product',
            value: '商品管理',
            path:'/product/takeaway/product/index'
        },
        {
            key: 'category',
            value: '分类管理',
            path:'/product/takeaway/category/index'
        },

      ],
        /*权限筛选后的数据*/
        tabList:[],
      };
    },
    watch:{
    
      //监听路由
      $route(to, from) {
        this.init();
      }
    },
    created() {
      this.init()
    
    },
    beforeDestroy() {
     //发送类别切换
     bus.$emit('tabData', { active: null, tab_type:'storeproduct',list: [] });
     bus.$off('activeValue');
    },
    methods: {
      /*初始化方法*/
      init(){
        this.tabList=this.authFilter();
    
        if(this.tabList.length>0){
          this.activeName=this.tabList[0].key;
        }
    
        if (this.$route.query.type != null) {
          this.activeName = this.$route.query.type;
        }
    
        /*监听传插件的值*/
        bus.$on('activeValue', res => {
          if (this.is_third_param) {
            this.param.user_id = '';
            this.is_third_param = false;
          }
          this.activeName = res;
        });
    
        //发送类别切换
        let params = {
          active: this.activeName,
          list: this.tabList,
          tab_type:'storeproduct'
        };
        bus.$emit('tabData', params);
      },
      /*权限过滤*/
      authFilter() {
        let list = [];
        for (let i = 0; i < this.sourceList.length; i++) {
          let item = this.sourceList[i];
          if (this.$filter.isAuth(item.path)) {
            list.push(item);
          }
        }
        return list;
      },
    
    }
  };
</script>

<style>
  .operation-wrap {
    height: 124px;
    border-radius: 8px;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    padding: 30px 30px;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    overflow: hidden;
    background: #909399;
    background-size: 100% 100%;
    color: #fff;
  }
</style>
