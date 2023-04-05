<template>
  <!--
    	作者：wangxw
    	时间：2019-10-26
    	描述：产品分类管理
    -->
  <div class="product">
    <div>
      <el-form size="small" :inline="true" :model="form" class="demo-form-inline d-s-c">
        <el-form-item label="编号">
          <el-input v-model="form.search" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="选择区域">
          <el-select size="small" v-model="form.area_id" placeholder="请选择">
            <el-option v-for="(item, index) in area_list" :key="index" :label="item.area_name" :value="item.area_id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="选择类型">
          <el-select size="small" v-model="form.type_id" placeholder="请选择">
            <el-option v-for="(item, index) in type_list" :key="index" :label="item.type_name" :value="item.type_id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--添加产品分类-->
    <div class="common-level-rail">
      <el-button size="small" type="primary" @click="addClick" icon="el-icon-plus" v-auth="'/store/table/table/add'">添加桌位</el-button>
    </div>
    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" row-key="type_id"  style="width: 100%" v-loading="loading">
          <el-table-column prop="area_name" label="所属区域"></el-table-column>
          <el-table-column prop="type_name" label="桌位类型"></el-table-column>
          <el-table-column prop="table_no" label="桌位编码"></el-table-column>
          <el-table-column prop="table_no" label="人数区间">
            <template slot-scope="scope">
              {{scope.row.min_num}}-{{scope.row.max_num}}人
            </template>
          </el-table-column>
          <el-table-column prop="sort" label="排序"></el-table-column>
          <el-table-column prop="create_time" label="添加时间"></el-table-column>
          <el-table-column fixed="right" label="操作" width="190">
            <template slot-scope="scope">
              <el-button @click="qrcodeClick(scope.row)" type="text" size="small"
                v-auth="'/store/table/table/edit'">小程序码</el-button>
              <el-button @click="editClick(scope.row)" type="text" size="small"
                v-auth="'/store/table/table/edit'">编辑</el-button>
                <el-button @click="deleteClick(scope.row)" type="text" size="small" v-auth="'/store/table/table/delete'">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <!--添加-->
    <Add v-if="open_add" :open_add="open_add" :type='type_list' :area_list="area_list" :addform="categoryModel" @closeDialog="closeDialogFunc($event, 'add')">
    </Add>
    <!--修改-->
    <Edit v-if="open_edit" :open_edit="open_edit" :editform="categoryModel"  :type='type_list' :area_list="area_list"
      @closeDialog="closeDialogFunc($event, 'edit')"></Edit>

  </div>
</template>

<script>
  import StoreApi from '@/api/store.js';
  import Add from './add.vue';
  import Edit from './edit.vue';
  import qs from 'qs';
  export default {
    components: {
      Add,
      Edit
    },
    data() {
      return {
        /*是否加载完成*/
        loading: true,
        /*列表数据*/
        tableData: [],
        /*是否打开添加弹窗*/
        open_add: false,
        /*是否打开编辑弹窗*/
        open_edit: false,
        /*当前编辑的对象*/
        categoryModel: {
          model:''
        },
        form:{search:'',area_id:'',type_id:''},
        type_list:[],
        area_list:[],
        source: 'wx',
      };
    },
    created() {
      /*获取列表*/
      this.getData();
    },
    methods: {
      handleClick() {
        this.getData();
      },
      /*获取列表*/
      getData() {
        let self = this;
        self.loading = true;
        let params= self.form
          StoreApi.tablelist(params, true)
            .then(data => {
              self.loading = false;
              self.tableData = data.data.list.data;
              self.type_list = data.data.type_list;
              self.area_list = data.data.area_list;
              self.categoryModel = data.data.list.data;
            })
            .catch(error => {
              self.loading = false;
            });
      },
      /*打开添加*/
      addClick() {
        this.open_add = true;
      },

      /*打开编辑*/
      editClick(item) {
        this.categoryModel.model = item;
        this.open_edit = true;
      },

      /*关闭弹窗*/
      closeDialogFunc(e, f) {
        if (f == 'add') {
          this.open_add = e.openDialog;
          if (e.type == 'success') {
            this.getData();
          }
        }
        if (f == 'edit') {
          this.open_edit = e.openDialog;
          if (e.type == 'success') {
            this.getData();
          }
        }
      },
      /*删除分类*/
      deleteClick(row) {
        let self = this;
        self.$confirm('删除后不可恢复，确认删除该记录吗?', '提示', {
          type: 'warning'
        }).then(() => {
          StoreApi.deleteTable({
            table_id: row.table_id
          }).then(data => {
            self.$message({
              message: '删除成功',
              type: 'success'
            });
            self.getData();
          });
        });
      },
      //小程序码
      qrcodeClick(row){
        let baseUrl = window.location.protocol + '//' + window.location.host;
        let params = {
          id: row.table_id,
          source: this.source
        };
        window.location.href = baseUrl + '/index.php/shop/store.table.table/qrcode?' + qs.stringify(params);
      },
      onSubmit(){
          this.curPage = 1;
          this.getData();
      }
    }
  };
</script>

<style></style>
