<template>

  <div class="product-add">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="150px">
      <!--添加门店-->
      <div class="common-form">短信通知（阿里云短信）</div>
      </el-form-item>
      <el-form-item label="AccessKey">
        <el-input v-model="form.AccessKeyId" class="max-w460"></el-input>
      </el-form-item>
      <el-form-item label="AccessKeySecret">
        <el-input v-model="form.AccessKeySecret" class="max-w460"></el-input>
      </el-form-item>
      <el-form-item label="短信签名">
        <el-input v-model="form.sign" class="max-w460"></el-input>
      </el-form-item>
      <!--提交-->
      <div class="common-button-wrapper">
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
        /*form表单数据*/
        form: {
          AccessKeyId: '',
          AccessKeySecret: '',
          sign: '',
          accept_phone: '',
          login_template: '',
          apply_template: '',
          supplier_reject_code:'',
          supplier_pass_code:''
        },
        loading: false,
      };
    },
    created() {
      this.getData()
    },

    methods: {
      getData() {
        let self = this;
        SettingApi.smsDetail({}, true)
          .then(data => {
            let vars = data.data.vars.values;
            self.form.AccessKeyId = vars.engine.aliyun.AccessKeyId;
            self.form.AccessKeySecret = vars.engine.aliyun.AccessKeySecret;
            self.form.sign = vars.engine.aliyun.sign;
            self.form.accept_phone = vars.engine.aliyun.accept_phone;
            self.form.template_code = vars.engine.aliyun.template_code;
            self.form.login_template = vars.engine.aliyun.login_template;
            self.form.apply_template = vars.engine.aliyun.apply_template;
            self.form.supplier_reject_code = vars.engine.aliyun.supplier_reject_code;
            self.form.supplier_pass_code = vars.engine.aliyun.supplier_pass_code;
          })
          .catch(error => {});

      },
      //提交表单
      onSubmit() {
        let self = this;
        self.loading = true;
        let params = this.form;
        SettingApi.editSms(params, true)
          .then(data => {
            self.loading = false;
            self.$message({
              message: '恭喜你，短信通知设置成功',
              type: 'success'
            });

          })
          .catch(error => {
            self.loading = false;
          });
      },
      //发送短信
      sendOut() {
        let self = this;
        let params = this.form;
        SettingApi.sendSms(params, true)
          .then(data => {
            self.$message({
              message: '恭喜你，短信发送成功',
              type: 'success'
            });
            self.$router.push('/setting/Sms');

          })
          .catch(error => {

          });
      }



    }

  };
</script>

<style>
  .tips {
    color: #ccc;
  }
</style>
