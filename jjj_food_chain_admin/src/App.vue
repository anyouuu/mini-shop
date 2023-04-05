<template>
  <div id="app"><router-view /></div>
</template>

<script>
export default {
  name: 'App'
};
</script>

<style lang="scss">
  @import '/assets/font/iconfont.css';
  @import '/assets/font/myIcon.css';
/*cover*/
.el-button,
.el-table,
.el-form-item__label,
.el-form-item__content,
.el-input,
.el-radio,
.el-radio--medium.is-bordered .el-radio__label,
.el-radio__label,
.el-select-dropdown__item,
.el-checkbox__label,
.el-link--inner,
.el-cascader__dropdown,
.el-cascader-panel{
  font-size: 12px;
}
</style>
