<template>
  <el-dialog title="查看物流" :visible.sync="dialogVisible"  :close-on-click-modal="false"
    :close-on-press-escape="false" width="900px">
      <el-input class="mb16" size="small" v-model="addressData.name" placeholder="请输入收货人"></el-input>
      <el-input class="mb16" size="small" v-model="addressData.phone" placeholder="请输入收获电话"></el-input>
      <el-select class="mb16" v-model="addressData.province_id" placeholder="省" @change="initCity">
        <el-option :label="item.name" :value="item.id" v-for="(item,index) in areaList" :key='index'></el-option>
      </el-select>
      <el-select v-if="addressData.province_id!=''" v-model="addressData.city_id" placeholder="市" @change="initRegion">
        <el-option :label="item1.name" :value="item1.id" v-for="(item1,index1) in areaList[addressData.province_id]['city']"
          :key='index1'></el-option>
      </el-select>
      <el-select v-if="addressData.city_id!=''" v-model="addressData.region_id" placeholder="区">
        <el-option :label="item2.name" :value="item2.id" v-for="(item2,index2) in areaList[addressData.province_id]['city'][addressData.city_id]['region']"
          :key='index2'></el-option>
      </el-select>
      <div slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogFormVisible(false)">取 消</el-button>
        <el-button size="small" type="primary" @click="dialogFormVisible(true)">确 定</el-button>
      </div>
  </el-dialog>
</template>

<script>
  import select_region from '@/utils/select.region.js';
  import OrderApi from '@/api/order.js';
  export default {
    data() {
      return {
        /*是否显示*/
        dialogVisible: false,
        /*结果类别*/
        type: 'error',
        /*传出去的参数*/
        params: null,
        reverse: false,
        order_id:0,
        activities: [],
        /*省市区*/
        areaList: select_region,
        address:{
          name:'',
          phone:'',
          region:{
            province:'',
            province_id:0,
            city:'',
            city_id:0,
            region:'',
            region_id:0,
            detail:''
          }
      }
    }},
    props:['isChange','addressData'],
    watch: {
      isChange: function(n, o) {
        if (n != o) {
            this.dialogVisible = n;
            this.type = 'error';
        }
      },
    },
    mounted() {
    },
    methods: {
    /*初始化城市id*/
    initCity() {
      this.addressData.city_id = ''
    },

    /*初始化区id*/
    initRegion() {
      this.addressData.region_id = ''
    },
      /*关闭弹窗*/
      dialogFormVisible(flag) {
        console.log(flag)
        if(flag){
          this.$emit('closeDialog', {
            type: this.type,
            openDialog: false,
            params: this.addressData
          });
        }else{
          this.$emit('closeDialog',false)
        }

      },
    }
  }
</script>

<style>

</style>
