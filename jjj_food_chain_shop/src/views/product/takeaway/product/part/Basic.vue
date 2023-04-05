<template>
  <!--
    	作者：luoyiming
    	时间：2019-10-26
    	描述：商品管理-商品编辑-基础信息
    -->
  <div class="basic-setting-content pl16 pr16">
    <!--基本信息-->
    <div class="common-form">基本信息</div>
    <el-form-item label="商品名称：" :rules="[{ required: true, message: '请填写商品名称' }]" prop="model.product_name">
      <el-input v-model="form.model.product_name" class="max-w460"></el-input>
    </el-form-item>
    <el-form-item label="所属分类：" :rules="[{ required: true, message: '你选择商品分类' }]" prop="model.category_id">
      <el-select v-model="form.model.category_id">
        <template>
          <el-option v-for="cat in formData.category" v-if="formData.category.length>0" :value="cat.category_id"
            :key="cat.category_id" :label="cat.name"></el-option>
        </template>
      </el-select>
    </el-form-item>
    <el-form-item label="商品图片：" :rules="[{ required: true, message: '请上传商品图片' }]" prop="model.image">
      <div class="draggable-list">
        <draggable class="wrapper" v-model="form.model.image">
          <transition-group>
            <div class="item" v-for="(item, index) in form.model.image" :key="item.file_path">
              <img v-img-url="item.file_path" />
              <a href="javascript:void(0);" class="delete-btn" @click.stop="deleteImg(index)"><i
                  class="el-icon-close"></i></a>
            </div>
          </transition-group>
        </draggable>
        <div class="item img-select" @click="openProductUpload"><i class="el-icon-plus"></i></div>
      </div>
    </el-form-item>
    <el-form-item label="商品卖点：">
      <el-input type="textarea" v-model="form.model.selling_point" class="max-w460"></el-input>
    </el-form-item>

    <!--商品图片组件-->
    <Upload v-if="isProductUpload" :config="{ total: 9 }" :isupload="isProductUpload"
      @returnImgs="returnProductImgsFunc">上传图片</Upload>
  </div>
</template>

<script>
  import Upload from '@/components/file/Upload';
  import draggable from 'vuedraggable';
  export default {
    components: {
      Upload,
      draggable
    },
    data() {
      return {
        formData: {},
        isProductUpload: false
      };
    },
    inject: ['form'],
    created() {
      this.$set(this, 'formData', this.form)
      console.log(this.formData)
    },
    methods: {

      /*打开上传图片*/
      openProductUpload: function() {
        this.isProductUpload = true;
      },

      /*上传商品图片*/
      returnProductImgsFunc(e) {
        if (e != null) {
          let imgs = this.form.model.image.concat(e);
          this.$set(this.form.model, 'image', imgs);
        }
        this.isProductUpload = false;
      },

      /*删除商品图片*/
      deleteImg(index) {
        this.form.model.image.splice(index, 1);
      },
    }
  };
</script>

<style>
  .edit_container {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    line-height: 20px;
    color: #2c3e50;
  }

  .ql-editor {
    height: 400px;
  }

  .draggable-list {
    display: flex;
    justify-content: flex-start;
    flex-wrap: wrap;
  }

  .draggable-list .wrapper>span {
    display: flex;
    justify-content: flex-start;
    flex-wrap: wrap;
  }

  .draggable-list .item {
    position: relative;
    width: 110px;
    height: 110px;
    margin-top: 10px;
    margin-right: 10px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dddddd;
  }

  .draggable-list .delete-btn {
    position: absolute;
    top: 0;
    right: 0;
    width: 16px;
    height: 16px;
    background: red;
    line-height: 16px;
    font-size: 16px;
    color: #ffffff;
    display: none;
  }

  .draggable-list .item:hover .delete-btn {
    display: block;
  }

  .draggable-list .item img {
    position: absolute;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    max-height: 100%;
    max-width: 100%;
  }

  .draggable-list .img-select {
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px dashed #dddddd;
    font-size: 30px;
  }

  .draggable-list .img-select i {
    color: #409eff;
  }
</style>
