<template>
  <!--
    	作者：luoyiming
    	时间：2019-10-26
    	描述：商品管理-商品编辑-规格/库存
    -->
  <div>
    <!--规格设置-->
    <div class="common-form mt50">商品加料</div>
    <!--多规格-->
    <div>
      <div class="mt16">
        <!-- <el-form-item label="属性明细："> -->
        <div class="p-0-30 mb18">
          <div class="d-s-c ">商品加料: <div class="blue ml30" @click="addIngredients">添加加料+</div>
          </div>
        </div>

        <!--多规格表格-->
        <div class="p-0-30 mb18" v-for="(item,index) in form.model.ingredients_list" :key="index"
          v-if="form.model.ingredients_list.length>0">
          <div class="d-c-c mb16">
            <div style="width: 100px;"><span class="red">*</span>加料名称：</div>
            <div class="flex-1"><span class="red">*</span>价格</div>
          </div>
          <div class="d-s-c">
            <div style="width: 100px;">
              <el-input size="medium" v-model="item.name" placeholder="如:杯型"></el-input>
            </div>
            <div class="d-s-c ml20">
                <el-input type="number" style="width: 100px;" size="medium" v-model="item.price" placeholder=""></el-input> <span class="ml10">元</span>
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
      addIngredients() {
        this.form.model.ingredients_list.push({
          name: '',
          price: ''
        })
      }
    }
  };
</script>

<style>

</style>
