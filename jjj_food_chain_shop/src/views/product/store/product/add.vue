<template>
  <div class="product-add">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="180px">
      <!--基础信息-->
      <Basic></Basic>

      <!--规格设置-->
      <Spec></Spec>

      <!-- 属性设置-->
      <Attr></Attr>

      <!-- 加料设置-->
      <Ingredients></Ingredients>

      <!--商品详情-->
      <Content></Content>

      <!--高级设置-->
      <Buyset></Buyset>

      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="info" @click="cancelFunc">取消</el-button>
        <el-button size="small" type="primary" @click="onSubmit" :loading="loading">发布</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
  import PorductApi from '@/api/product.js';
  import Basic from './part/Basic.vue';
  import Attr from './part/Attr.vue';
  import Ingredients from './part/Ingredients.vue';
  import Spec from './part/Spec.vue';
  import Content from './part/Content.vue';
  import Buyset from './part/Buyset.vue';
  export default {
    components: {
      /*基础信息*/
      Basic,
      /*规格信息*/
      Spec,
      /* 属性信息*/
      Attr,
      /*加料设置*/
      Ingredients,
      /*商品详情*/
      Content,
      /*高级设置*/
      Buyset
    },
    data() {
      return {
        /*是否正在加载*/
        loading: false,
        /*form表单数据*/
        form: {
          model: {
            /*商品名称*/
            product_name: '',
            /*商品分类*/
            category_id: null,
            /*商品图片*/
            image: [],
            /*商品卖点*/
            selling_point: '',
            /*规格类别,默认10单规格，20多规格*/
            spec_type: 10,
            /*库存计算方式,默认20付款减库存，10下单减库存*/
            deduct_stock_type: 20,
            /*检查用户等级*/
            is_alone_grade: 0,
            sku: [{
              product_price: '',
              stock_num: '',
              product_weight: '',
              cost_price:0,
            }],
            product_attr: [],
            product_feed: [],
            /* 最小购买量 */
            min_buy: 1,
            /* 商品单位 */
            product_unit: '',
            /* 属性*/
            /*商品详情内容*/
            content: '',
            /*商品状态*/
            product_status: 10,
            /*初始销量*/
            sales_initial: 0,
            /*商品排序，默认100*/
            product_sort: 100,
            /*限购数量*/
            limit_num: 0,
            special_id: 0,
          
          },
          /*商品分类*/
          category: [],
          feed: [],
          special: [],
          /*运费模板*/
          delivery: [],
          /*会员等级*/
          gradeList: [],
          /*规格数据*/
          specData: null,
        }
      };
    },
    provide: function() {
      return {
        form: this.form
      }
    },
    created() {

      /*获取基础数据*/
      this.getBaseData();

    },
    methods: {

      /*获取基础数据*/
      getBaseData: function() {
        let self = this;
        PorductApi.storeGetBaseData({}, true)
          .then(res => {
            self.loading = false;
            Object.assign(self.form, res.data);
          })
          .catch(error => {
            self.loading = false;
          });
      },

      /*转JSON字符串*/
      convertJson(list) {
        let obj = {};
        list.forEach(item => {
          obj[item.grade_id] = item.product_equity;
        });
        return JSON.stringify(obj);
      },

      /*提交*/
      onSubmit: function() {
        let self = this;
        let params = self.form.model;
        self.$refs.form.validate(valid => {
          if (valid) {
            self.loading = true;
            params.alone_grade_equity = self.convertJson(self.form.gradeList);
            PorductApi.storeAddProduct({
                params: JSON.stringify(params)
              }, true)
              .then(data => {
                self.loading = false;
                self.$message({
                  message: '添加成功',
                  type: 'success'
                });
                self.$router.push('/product/store/product/index');
              })
              .catch(error => {
                self.loading = false;
              });
          }
        });
      },

      /*保存为草稿*/
      Draft() {
        let self = this;
        self.form.model.product_status = 30;
        self.onSubmit();
      },

      /*取消*/
      cancelFunc() {
        this.$router.back(-1);
      }

    }
  };
</script>

<style lang="scss" scoped>
  .basic-setting-content {}

  .product-add {
    padding-bottom: 100px;
  }
</style>
