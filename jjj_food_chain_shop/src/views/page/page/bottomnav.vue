<template>
  <!--
      作者 luoyiming
      时间：2019-10-26
      描述：设置-商城设置
  -->
  <div class="product-add">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="150px">
      <div class="common-form">底部中心菜单设置</div>
      <el-form-item label="菜单名称">
        <el-radio-group v-model="form.menu_type">
          <el-radio :label="1">店铺</el-radio>
          <el-radio :label="2">订单</el-radio>
        </el-radio-group>
      </el-form-item>
      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="primary" @click="onSubmit" :loading="loading">提交</el-button>
      </div>
    </el-form>
  </div>

</template>

<script>
  import PageApi from '@/api/page.js';
  import Upload from '@/components/file/Upload';
  import { formatModel } from '@/utils/base.js';
  export default {
    data() {
      return {
        /*是否正在加载*/
        loading: false,
        /*form表单数据*/
        form: {
         menu_type:1,
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
        PageApi.getbottomNav({}, true).then(res => {
            self.form=res.data.data;
            self.form.menu_type = parseInt(self.form.menu_type);
            self.loading=false;
          })
          .catch(error => {

          });
      },


      /*提交*/
      onSubmit() {
        let self = this;
        let params = this.form;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            PageApi.postbottomNav(params, true)
              .then(data => {
                self.loading = false;
                self.$message({
                  message: '恭喜你，设置成功',
                  type: 'success'
                });
                self.$router.push('/page/page/bottomnav');
              })
              .catch(error => {
                self.loading = false;
              });
          }
        });

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
