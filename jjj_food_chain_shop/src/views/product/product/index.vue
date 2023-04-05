<template>
  <!--
      作者：luoyiming
      时间：2019-10-24
      描述：商品管理
  -->
  <div class="product-list">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-tabs v-model="activeName" @tab-click="handleClick">
        <el-tab-pane label="出售中" name="sell"></el-tab-pane>
        <el-tab-pane label="强制下架" name="lower">
          <span slot="label">强制下架 <el-tag size="mini">{{product_count.lower}}</el-tag></span>
        </el-tab-pane>
        <el-tab-pane label="待审核" name="audit">
          <span slot="label">待审核 <el-tag size="mini">{{product_count.audit}}</el-tag></span>
        </el-tab-pane>
        <el-tab-pane label="未通过" name="no_audit">
          <span slot="label">未通过 <el-tag size="mini">{{product_count.no_audit}}</el-tag></span>
        </el-tab-pane>
      </el-tabs>
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="商品分类">
          <el-select size="small" v-model="searchForm.category_id" placeholder="所有分类">
            <el-option label="全部" value="0"></el-option>
            <el-option v-for="(item, index) in categoryList" :key="index" :label="item.name" :value="item.category_id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="商品名称"><el-input size="small" v-model="searchForm.product_name" placeholder="请输入商品名称"></el-input></el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="el-icon-search" @click="onSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" border style="width: 100%" v-loading="loading">
          <el-table-column prop="product_name" label="产品" width="400px">
            <template slot-scope="scope">
              <div class="product-info">
                <div class="pic"><img v-img-url="scope.row.image[0].file_path" alt="" /></div>
                <div class="info">
                  <div class="name">{{ scope.row.product_name }}</div>
                  <div class="price">销售价：{{ scope.row.product_price }}</div>
                  <div class="price">供应价：{{ scope.row.supplier_price }}</div>
                </div>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="supplier.name" label="商户名称"></el-table-column>
          <el-table-column prop="category.name" label="分类"></el-table-column>
          <el-table-column prop="sales_actual" label="实际销量"></el-table-column>
          <el-table-column prop="product_stock" label="库存"></el-table-column>
          <el-table-column prop="view_times" label="点击数"></el-table-column>
          <el-table-column prop="product_status.text" label="状态">
            <template slot-scope="scope">
              <span :class="{ green: scope.row.product_status.value == 10, gray: scope.row.product_status.value == 20 }">{{ scope.row.product_status.text }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="product_status.text" label="商品属性">
            <template slot-scope="scope">
              <span v-if="scope.row.is_virtual == 0">实物商品</span>
              <span v-if="scope.row.is_virtual == 1" class="green">虚拟商品</span>
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="发布时间"></el-table-column>
          <el-table-column prop="product_sort" label="排序"></el-table-column>
          <el-table-column fixed="right" label="操作" width="120">
            <template slot-scope="scope">
              <el-button @click="editClick(scope.row)" type="text" size="small" v-auth="'/product/product/edit'" v-if="scope.row.audit_status==10">编辑</el-button>
              <el-button @click="editClick(scope.row)" type="text" size="small" v-auth="'/product/product/edit'" v-else>审核</el-button>
              <el-button @click="undercarriage(scope.row,30)" type="text" size="small" v-auth="'/product/product/delete'" v-if="scope.row.audit_status!=30">强制下架</el-button>
              <el-button @click="undercarriage(scope.row,10)" type="text" size="small" v-auth="'/product/product/delete'" v-else>重新上架</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <!--分页-->
    <div class="pagination">
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        background
        :current-page="curPage"
        :page-size="pageSize"
        layout="total, prev, pager, next, jumper"
        :total="totalDataNumber"
      ></el-pagination>
    </div>
  </div>
</template>

<script>
import PorductApi from '@/api/product.js';
export default {
  components: {},
  data() {
    return {
      /*切换菜单*/
      activeName: 'sell',
      /*切换选中值*/
      activeIndex: '0',
      /*是否正在加载*/
      loading: true,
      /*一页多少条*/
      pageSize: 10,
      /*一共多少条数据*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1,
      /*搜索参数*/
      searchForm: {
        product_name: '',
        category_id: ''
      },
      /*列表数据*/
      tableData: [],
      /*全部分类*/
      categoryList: [],
      /*商品统计*/
      product_count: {}
    };
  },
  created() {
    /*获取列表*/
    this.getData();
  },
  methods: {
    /*选择第几页*/
    handleCurrentChange(val) {
      let self = this;
      self.loading = true;
      self.curPage = val;
      self.getData();
    },

    /*每页多少条*/
    handleSizeChange(val) {
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
      let Params = self.searchForm;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      Params.type = self.activeName;
      self.loading = true;
      PorductApi.productList(Params, true)
        .then(data => {
          self.loading = false;
          self.tableData = data.data.list.data;
          self.categoryList = data.data.category;
          self.totalDataNumber = data.data.list.total;
          self.product_count = data.data.product_count;
        })
        .catch(error => {
          self.loading = false;
        });
    },

    /*搜索查询*/
    onSubmit() {
      this.curPage = 1;
      this.getData();
    },

    /*打开添加*/
    addClick() {
      this.$router.push('/product/product/add');
    },

    /*打开编辑*/
    editClick(row) {
      this.$router.push({
        path: '/product/product/edit',
        query: {
          product_id: row.product_id,
          scene: 'edit'
        }
      });
    },
    /* 强制下架上架*/
    undercarriage(row,state){
      let self = this;
      let war="";
      let war_='';
      if(state==20){
        war="强制下架",
        war_='下架'
      }else if(state==10){
        war="重新上架",
        war_='上架'
      }
      self
        .$confirm("确认要"+war+"吗?", '提示', {
          type: 'warning'
        })
        .then(() => {
          PorductApi.auditProduct({
            product_id: row.product_id,
            state
          }).then(data => {
            self.$message({
              message: war_+'成功',
              type: 'success'
            });
            self.getData();
          });
        });
    },
    /*打开复制*/
    copyClick(row) {
      this.$router.push({
        path: '/product/product/edit',
        query: {
          product_id: row.product_id,
          scene: 'copy'
        }
      });
    },

    /*删除*/
    delClick: function(row) {
      let self = this;
      self
        .$confirm('删除后不可恢复，确认删除该记录吗?', '提示', {
          type: 'warning'
        })
        .then(() => {
          PorductApi.delProduct({
            product_id: row.product_id
          }).then(data => {
            self.$message({
              message: '删除成功',
              type: 'success'
            });
            self.getData();
          });
        });
    }
  }
};
</script>

<style></style>
