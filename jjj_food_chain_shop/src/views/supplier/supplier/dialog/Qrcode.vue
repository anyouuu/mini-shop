<template>
  <!--
      	作者：luoyiming
      	时间：2020-06-01
      	描述：插件中心-分销-提现申请-弹窗
      -->
  <div>
    <el-dialog title="下载二维码" width="35%" :visible.sync="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false" :close-on-press-escape="false">
      <div class="d-a-c">
        <el-button @click="qrcodeClick" type="primary" plain>小程序码下载</el-button>
        <!-- <el-button type="primary" plain>H5二维码下载</el-button> -->
      </div>
      <div slot="footer" class="dialog-footer">
      </div>
    </el-dialog>
  </div>
</template>

<script>
import SupplierApi from '@/api/supplier.js';
import qs from 'qs';
export default {
  data() {
    return {
      status: '',
      reject_reason: '',
      /*左边长度*/
      formLabelWidth: '120px',
      /*是否显示*/
      dialogVisible: false,
      loading:false
    };
  },
  props: ['open','code_id'],
  watch:{
    open:function(n,o){
       this.dialogVisible = this.open;
    }
  },
  created() {

  },
  methods: {
    qrcodeClick(){
      let baseUrl = window.location.protocol + '//' + window.location.host;
      let params = {
        shop_supplier_id: this.code_id,
        source: 'wx'
      };
      window.location.href = baseUrl + '/index.php/shop/supplier.supplier/qrcode?' + qs.stringify(params);
    },
    /*审核*/
    cashSubmit() {
      let self = this;
      let params = this.form;
      SupplierApi.cashSubmit(params, true)
        .then(data => {
          self.$message({
            message: '恭喜你，修改成功',
            type: 'success'
          });
          self.dialogFormVisible(true);
        })
        .catch(error => {});
    },

    /*关闭弹窗*/
    dialogFormVisible(e) {
      if (e) {
        this.$emit('close', true);
      } else {
        this.$emit('close', false);
      }
    }
  }
};
</script>

<style></style>
