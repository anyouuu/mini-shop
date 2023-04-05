<template>
  <!--
      作者 luoyiming
      时间：2019-10-26
      描述：设置-商城设置
  -->
  <div class="product-add">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="150px">
      <!--添加门店-->
      <div class="common-form">商城设置</div>
      <el-form-item label="商城名称" :rules="[{required: true,message: ' '}]" prop="name">
        <el-input v-model="form.name" placeholder="商城名称" class="max-w460"></el-input>
      </el-form-item>
      <div class="common-form">日志记录</div>
      <el-form-item label="是否记录查询日志" prop="customer">
        <el-checkbox v-model="form.is_get_log">是否记录查询日志</el-checkbox>
        <div class="tips">如果记录，日志量会有点大</div>
      </el-form-item>
      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
      </div>
    </el-form>
<!--上传图片-->
    <Upload v-if="isupload" :isupload="isupload" :type="type" :config="{ total: 3 }" @returnImgs="returnImgsFunc"></Upload>

  </div>

</template>

<script>
  import SettingApi from '@/api/setting.js';
  import Upload from '@/components/file/Upload';
  import { formatModel } from '@/utils/base.js';
  export default {
    components:{
      Upload
    },
    data() {
      return {
        /*是否正在加载*/
        loading: false,
        /*form表单数据*/
        form: {
          name: '',
          customer: '',
          key: '',
          supplier_cash:'',
          operate_type:'',
          commission_rate:'',
          supplier_image:'',
          sms_open:'',
          supplier_logo:'',
          checkedCities: [],
          edit_audit: 1,
          add_audit: 1,
          is_get_log: 0
        },
        all_type: [],
        type: [],
        /*是否打开图片选择*/
        isupload:false
      };
    },
    created() {
      this.getParams()
    },

    methods: {

      /*获取配置数据*/
      getParams() {
        let self = this;
        SettingApi.storeDetail({}, true).then(res => {
            let vars = res.data.vars.values;
            self.form = formatModel(self.form, vars);

            self.form.checkedCities = res.data.vars.values.delivery_type;
            // 转成整数，兼容组件
            for(let i=0;i<self.form.checkedCities.length;i++){
              self.$set(self.form.checkedCities, i, parseInt(self.form.checkedCities[i]));
            }
            self.type = vars.delivery_type;
            self.form.customer = vars.kuaidi100.customer;
            self.form.key = vars.kuaidi100.key;
            self.all_type = res.data.all_type;
            self.loading=false;
          })
          .catch(error => {

          });
      },


      /*提交*/
      onSubmit() {
        let self = this;
        let params = this.form;
        if (params.checkedCities.length < 1) {
          self.$message({
            message: '配送方式至少选择一种！',
            type: 'warning'
          });
          return;
        }

        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            SettingApi.editStore(params, true)
              .then(data => {
                self.loading = false;
                self.$message({
                  message: '恭喜你，商城设置成功',
                  type: 'success'
                });
                self.$router.push('/setting/store/index');
              })
              .catch(error => {
                self.loading = false;
              });
          }
        });

      },
  　　renumber(e){
      var keynum=window.event ? e.keyCode :e.which;
      var keycar=String.fromCharCode(keynum);
      if(keynum==189||keynum==190||keynum==110||keynum==109){
        this.$message.warning("禁止输入小数和负数")
        e.target.value=''
      }
  　　},
      /*选择图片*/
      chooseImg(e){
        this.type = e;
        this.isupload=true;
      },

      /*关闭选择图片*/
      returnImgsFunc(e){
        this.isupload=false;
         if (e != null && e.length > 0) {
        if(this.type == 'logo'){
           this.form.supplier_logo=e[0].file_path;
        }else if(this.type == 'image'){
           this.form.supplier_image=e[0].file_path;
        }
        }
      }
    }

  };
</script>
<style>
  .tips {
    color: #ccc;
  }
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
  }
  input[type="number"]{
    -moz-appearance: textfield;
  }
</style>
