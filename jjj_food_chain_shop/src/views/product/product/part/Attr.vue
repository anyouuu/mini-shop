<template>
  <!--
    	作者：luoyiming
    	时间：2019-10-26
    	描述：商品管理-商品编辑-规格/库存
    -->
  <div>
    <!--规格设置-->
    <div class="common-form mt50">商品属性</div>
    <!--多规格-->
    <div>
      <div class="mt16">
        <!-- <el-form-item label="属性明细："> -->
        <div class="p-0-30 mb18">
          <div class="d-s-c ">商品属性: <div class="blue ml30" @click="addAttr">添加属性+</div>
          </div>
        </div>

        <!--多规格表格-->
        <div class="mb18 p-0-30" v-for="(item,index) in form.model.attr_many.attr_list" :key="index"
          v-if="form.model.attr_many.attr_list.length>0">
          <div class="d-c-c mb16">
            <div style="width: 100px;"><span class="red">*</span>属性名称：</div>
            <div class="flex-1"><span class="red">*</span>属性值(至少填写两个)</div>
          </div>
          <div class="d-c-c">
            <div style="width: 100px;">
              <el-input size="medium" v-model="item.name" placeholder="如:温度"></el-input>
            </div>
            <div class="flex-1 d-s-c">
              <div v-for="(aitem,aindex) in 8" :key="aindex" class="ml20">
                <el-input style="width: 100px;" size="medium" v-model="item.attrs[aindex]" placeholder="请选择"></el-input>
              </div>
            </div>
          </div>
        </div>
        <!-- </el-form-item> -->
      </div>
    </div>

  </div>
</template>

<script>
  export default {
    inject: ['form'],
    methods: {
      addAttr() {
        this.form.model.attr_many.attr_list.push({
          name: '',
          attrs: []
        })
      }
    }
  };
</script>

<style>

</style>
