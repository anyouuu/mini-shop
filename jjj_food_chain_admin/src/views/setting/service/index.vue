<template>
  <!--
      作者 wangxw
      时间：2021-01-14
      描述：设置-商城设置
  -->
  <div class="product-add">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="150px">
      <div class="common-form">商城后台设置</div>
      <el-form-item label="商城系统名称" :rules="[{required: true,message: ' '}]" prop="shop_name">
        <el-input v-model="form.shop_name" placeholder="商城名称" class="max-w460"></el-input>
        <div class="tips">
          shop端商城名称，显示在登录页
        </div>
      </el-form-item>
      <el-form-item label="商城登录背景" prop="shop_bg_img">
        <el-input v-model="form.shop_bg_img" placeholder="商城登录背景" class="max-w460"></el-input>
        <div class="tips">
          shop端商城登录背景，不填则为系统默认登录背景，填写网络地址
        </div>
      </el-form-item>
      <!--提交-->
      <div class="button-wrapper">
        <el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
  import SettingApi from '@/api/setting.js';
  export default {
    data() {
      return {
        /*是否正在加载*/
        loading: false,
        /*form表单数据*/
        form: {
          url: '',
          service_open:1
        },

      };
    },
    created() {
      this.getParams()
    },

    methods: {

      /*获取配置数据*/
      getParams() {
        let self = this;
        SettingApi.serviceDetail({}, true).then(res => {
            self.form = res.data.vars.values;
            self.form.service_open = (vars.service_open)*1;
            self.loading=false;
          })
          .catch(error => {
            self.loading=false;
          });

      },
      /*提交*/
      onSubmit() {
        let self = this;
        let params = this.form;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            SettingApi.editService(params, true)
              .then(data => {
                self.loading = false;
                self.$message({
                  message: '恭喜你，设置成功',
                  type: 'success'
                });
                self.$router.push('/setting/Index');
              })
              .catch(error => {
                self.loading = false;
              });
          }
        });
      },
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
  .button-wrapper{
    display: flex;
  }
</style>
