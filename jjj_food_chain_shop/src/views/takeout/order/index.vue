<template>
  <!--
      作者：luoyiming
      时间：2019-10-25
      描述：订单列表
  -->
  <div class="user">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="订单号">
          <el-input size="small" v-model="searchForm.order_no" placeholder="请输入订单号"></el-input>
        </el-form-item>
        <el-form-item label="配送方式">
          <el-select size="small" v-model="searchForm.style_id" placeholder="请选择">
            <el-option label="全部" value=""></el-option>
            <el-option v-for="(item, index) in exStyle" :key="index" :label="item.name" :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="起始时间">
          <div class="block">
            <span class="demonstration"></span>
            <el-date-picker size="small" v-model="searchForm.create_time" type="daterange" value-format="yyyy-MM-dd"
              range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
          </div>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="el-icon-search" @click="onSubmit">查询</el-button>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="success" @click="onExport">导出</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="全部订单" name="all"></el-tab-pane>
          <el-tab-pane :label="'未付款'" name="payment">
            <span slot="label">未付款 <el-tag size="mini">{{order_count.payment}}</el-tag></span>
          </el-tab-pane>
          <el-tab-pane :label="'进行中'" name="process">
            <span slot="label">进行中 <el-tag size="mini">{{order_count.process}}</el-tag></span>
          </el-tab-pane>
          <el-tab-pane :label="'已完成'" name="complete">
            <span slot="label">已完成 <el-tag size="mini">{{order_count.complete}}</el-tag></span>
          </el-tab-pane>
        </el-tabs>
        <el-table size="small" :data="tableData.data" :span-method="arraySpanMethod" border style="width: 100%"
          v-loading="loading">
          <el-table-column prop="order_no" label="订单信息" width="400">
            <template slot-scope="scope">
              <div class="order-code" v-if="scope.row.is_top_row">
                <span class="state-text"
                  :class="{'state-text-red':scope.row.order_source != 10}">{{scope.row.order_source_text}}</span>
                <span class="c_main">订单号：{{ scope.row.order_no }}</span>
                <span class="pl16">下单时间：{{ scope.row.create_time }}</span>
                <!--是否取消申请中-->
                <span class="pl16" v-if="scope.row.order_status == 21">
                  <el-tag effect="dark" size="mini">取消申请中</el-tag>
                </span>
              </div>
              <template v-else>
                <div class="product-info" v-for="(item, index) in scope.row.product" :key="index">
                  <div class="pic"><img v-img-url="item.image.file_path" alt="" /></div>
                  <div class="info">
                    <div class="name gray3 product-name">
                      <span>{{ item.product_name }}</span>
                    </div>
                    <div class="gray9" v-if="item.product_attr">{{ item.product_attr }}</div>
                    <div class="orange" v-if="item.refund">{{ item.refund.type.text }}-{{ item.refund.status.text }}
                    </div>
                  </div>
                  <div class="d-c-c d-c">
                    <div class="orange">￥ {{ item.product_price }}</div>
                    <div class="gray3">x{{ item.total_num }}</div>
                  </div>
                </div>
              </template>
            </template>
          </el-table-column>
          <el-table-column prop="pay_price" label="实付款">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              <div class="orange">{{ scope.row.pay_price }}</div>
              <p class="gray9">(含配送费：{{ scope.row.express_price }})</p>
              <p class="gray9">(含包装费：{{ scope.row.bag_price }})</p>
            </template>
          </el-table-column>
          <el-table-column prop="" label="买家">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              <div>{{ scope.row.user.nickName }}</div>
              <div class="gray9">ID：({{ scope.row.user.user_id }})</div>
            </template>
          </el-table-column>
          <el-table-column prop="supplier.name" label="门店名称"></el-table-column>
          <el-table-column prop="state_text" label="交易状态">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              {{ scope.row.state_text }}
            </template>
          </el-table-column>
          <el-table-column prop="pay_type.text" label="支付方式">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              <span class="gray9">{{ scope.row.pay_type.text }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="delivery_type.text" label="配送方式">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              <span class="green">{{ scope.row.delivery_type.text }}</span>
              <span v-if="scope.row.delivery_type.value == 30">手机号:{{scope.row.user.mobile}}</span>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="200">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              <el-button @click="addClick(scope.row)" type="text" size="small" v-auth="'/takeout/order/detail'">订单详情
              </el-button>
              <el-button v-if="scope.row.order_status.value==10&&scope.row.pay_status.value==20"
                @click="refundClick(scope.row)" type="text" size="small" v-auth="'/takeout/Operate/refund'">退款
              </el-button>
              <el-button
                v-if="scope.row.order_status.value==10&&scope.row.delivery_status.value==10&&scope.row.pay_status.value==20"
                @click="cancelClick(scope.row)" type="text" size="small" v-auth="'/takeout/operate/confirmCancel'">取消
              </el-button>
              <el-button
                v-if="scope.row.order_status.value==10&&scope.row.delivery_type.value==20&&scope.row.pay_status.value==20"
                @click="verifyClick(scope.row)" type="text" size="small" v-auth="'/takeout/operate/extract'">核销
              </el-button>
              <el-button
                v-if="scope.row.order_status.value==10&&scope.row.delivery_type.value==10&&scope.row.pay_status.value==20&&scope.row.delivery_status.value==20"
                @click="verifyClick(scope.row)" type="text" size="small" v-auth="'/takeout/operate/extract'">确认送达
              </el-button>
              <el-button @click="senDada(scope.row)"
                v-if="scope.row.pay_status.value==20&&scope.row.deliver_status==0&&scope.row.order_status.value==10&&scope.row.delivery_type.value==10"
                type="text" size="small" v-auth="'/takeout/operate/sendOrder'">{{deliver_name}}
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" background
          :current-page="curPage" :page-size="pageSize" layout="total, prev, pager, next, jumper"
          :total="totalDataNumber"></el-pagination>
      </div>
    </div>
    <Cancel v-if="open_edit" :open_edit="open_edit" :order_no="order_no" :order_id="order_id"
      @closeDialog="closeDialogFunc($event, 'edit')">
    </Cancel>
    <!--处理-->
    <refund v-if="open_refund" :open_edit="open_refund" :order_no="order_no" :order_id="order_id"
      @closeDialog="closerefundDialogFunc($event, 'edit')">
    </refund>
  </div>
</template>

<script>
  import OrderApi from '@/api/order.js';
  import Cancel from './dialog/cancel.vue';
  import refund from './dialog/refund.vue';
  import qs from 'qs';
  export default {
    components: {
      Cancel,
      refund,
    },
    data() {
      return {
        /*切换菜单*/
        activeName: 'all',
        /*是否加载完成*/
        loading: true,
        /*列表数据*/
        tableData: [],
        /*一页多少条*/
        pageSize: 20,
        /*一共多少条数据*/
        totalDataNumber: 0,
        /*当前是第几页*/
        curPage: 1,
        /*横向表单数据模型*/
        searchForm: {
          order_no: '',
          style_id: '',
          create_time: ''
        },
        /*配送方式*/
        exStyle: [],
        /*门店列表*/
        shopList: [],
        /*时间*/
        create_time: '',
        /*统计*/
        order_count: {
          payment: 0,
          delivery: 0,
          received: 0
        },
        /*是否打开编辑弹窗*/
        open_edit: false,
        open_refund: false,
        /*当前编辑的对象*/
        order_no: 0,
        deliver_name: '',
        order_id: 0
      };
    },
    created() {
      /*获取列表*/
      this.getData();
    },
    methods: {
      /*跨多列*/
      arraySpanMethod(row) {
        if (row.rowIndex % 2 == 0) {
          if (row.columnIndex === 0) {
            return [1, 8];
          }
        }
      },
      /*选择第几页*/
      handleCurrentChange(val) {
        let self = this;
        self.curPage = val;
        self.getData();
      },

      /*每页多少条*/
      handleSizeChange(val) {
        this.curPage = 1;
        this.pageSize = val;
        this.getData();
      },

      /*切换菜单*/
      handleClick(tab, event) {
        let self = this;
        self.curPage = 1;
        self.getData();
      },

      /*获取列表*/
      getData() {
        let self = this;
        let Params = this.searchForm;
        Params.dataType = self.activeName;
        Params.page = self.curPage;
        Params.list_rows = self.pageSize;
        self.loading = true;
        OrderApi.takeOrderlist(Params, true)
          .then(res => {
            let list = [];
            for (let i = 0; i < res.data.list.data.length; i++) {
              let item = res.data.list.data[i];
              let topitem = {
                order_no: item.order_no,
                create_time: item.create_time,
                order_source: item.order_source,
                order_source_text: item.order_source_text,
                is_top_row: true,
                order_status: item.order_status.value,
              };
              list.push(topitem);
              list.push(item);
            }
            self.tableData.data = list;
            self.deliver_name = res.data.deliver_name;
            self.totalDataNumber = res.data.list.total;
            self.exStyle = res.data.ex_style;
            self.order_count = res.data.order_count.order_count;
            self.loading = false;
          })
          .catch(error => {});
      },

      /*打开添加*/
      addClick(row) {
        let self = this;
        let params = row.order_id;
        self.$router.push({
          path: '/takeout/order/detail',
          query: {
            order_id: params
          }
        });
      },
      /*核销*/
      verifyClick(row) {
        let self = this;
        let extract_form = {};
        this.$confirm('是否确认此操作?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          extract_form.order_id = row.order_id;
          OrderApi.takeExtract(extract_form,
              true
            )
            .then(data => {
              self.loading = false;
              self.$message({
                message: '恭喜你，操作成功',
                type: 'success'
              });
              self.getData();
            })
            .catch(error => {
              self.loading = false;
            });
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消操作'
          });
        });

      },
      senDada(row) {
        let self = this;
        let extract_form = {};
        this.$confirm('是否确认此操作?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          extract_form.order_id = row.order_id;
          OrderApi.sendDada(extract_form,
              true
            )
            .then(data => {
              self.loading = false;
              self.$message({
                message: '恭喜你，操作成功',
                type: 'success'
              });
              self.getData();
            })
            .catch(error => {
              self.loading = false;
            });
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消操作'
          });
        });

      },
      /*搜索查询*/
      onSubmit() {
        this.curPage = 1;
        this.tableData = [];
        this.getData();
      },
      onExport: function() {
        let baseUrl = window.location.protocol + '//' + window.location.host;
        window.location.href = baseUrl + '/index.php/shop/takeout.operate/export?' + qs.stringify(this.searchForm);
      },
      /*打开取消*/
      cancelClick(item) {
        this.order_no = item.order_no;
        this.order_id = item.order_id;
        this.open_edit = true;
      },
      refundClick(item) {
        this.order_id = item.order_id;
        this.order_no = item.order_no;
        this.open_refund = true;
      },
      /*关闭弹窗*/
      closeDialogFunc(e, f) {
        if (f == 'edit') {
          this.open_edit = e.openDialog;
          if (e.type == 'success') {
            this.getData();
          }
        }
      },
      /*关闭弹窗*/
      closerefundDialogFunc(e, f) {
        if (f == 'edit') {
          this.open_refund = e.openDialog;
          if (e.type == 'success') {
            this.getData();
          }
        }
      },
    }
  };
</script>
<style lang="scss">
  .product-info {
    padding: 10px 0;
    border-top: 1px solid #eeeeee;
  }

  .order-code .state-text {
    padding: 2px 4px;
    border-radius: 4px;
    background: #808080;
    color: #ffffff;
  }

  .order-code .state-text-red {
    background: red;
  }

  .table-wrap .product-info:first-of-type {
    border-top: none;
  }

  .table-wrap .el-table__body tbody .el-table__row:nth-child(odd) {
    background: #f5f7fa;
  }
</style>
