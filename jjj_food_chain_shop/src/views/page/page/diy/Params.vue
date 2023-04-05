<template>
  <!--
    	作者：luoyiming
    	时间：2020-06-20
    	描述：diy组件-参数设置
    -->
  <div id="diy-editor" ref="diy-editor" class="diy-editor form-horizontal">
    <template v-show="diyData.items.length && form.curItem">
      <!--图片轮播-->
      <template v-if="form.curItem.type == 'banner'">
        <Banner :curItem="form.curItem" :selectedIndex="form.selectedIndex"></Banner>
      </template>
      <!--图片-->
      <template v-if="form.curItem.type == 'imageSingle'">
        <ImageSingle :curItem="form.curItem" :selectedIndex="form.selectedIndex"></ImageSingle>
      </template>
      <!--图片橱窗-->
      <template v-if="form.curItem.type == 'window'">
        <Window :curItem="form.curItem" :selectedIndex="form.selectedIndex"></Window>
      </template>
      <!--视频组件-->
      <template v-if="form.curItem.type == 'video'">
        <Video :curItem="form.curItem" :selectedIndex="form.selectedIndex"></Video>
      </template>
      <!--公告组-->
      <template v-if="form.curItem.type == 'notice'">
        <Notice :curItem="form.curItem" :selectedIndex="form.selectedIndex"></Notice>
      </template>
      <!--导航组-->
      <template v-if="form.curItem.type == 'navBar'">
        <NavBar :curItem="form.curItem" :selectedIndex="form.selectedIndex"></NavBar>
      </template>
      <!--商品组-->
      <template v-if="form.curItem.type == 'product'">
        <Product :curItem="form.curItem" :selectedIndex="form.selectedIndex"></Product>
      </template>
      <!--门店-->
      <template v-if="form.curItem.type == 'store'">
        <Store :curItem="form.curItem" :selectedIndex="form.selectedIndex"></Store>
      </template>
      <!--富文本-->
      <template v-if="form.curItem.type == 'richText'">
        <RichText :curItem="form.curItem" :selectedIndex="form.selectedIndex"></RichText>
      </template>
      <!--辅助空白-->
      <template v-if="form.curItem.type == 'blank'">
        <Blank :curItem="form.curItem" :selectedIndex="form.selectedIndex"></Blank>
      </template>
      <!--辅助线-->
      <template v-if="form.curItem.type == 'guide'">
        <Guide :curItem="form.curItem" :selectedIndex="form.selectedIndex"></Guide>
      </template>
    </template>

    <!--上传图片-->
    <Upload v-if="isupload" :isupload="isupload" :config="{ total: 3 }" @returnImgs="returnImgsFunc"></Upload>

    <!--选择商品-->
    <ProductSelect :isproduct="isproduct" :excludeIds="excludeIds" :islist="islist" @closeDialog="closeProductDialogFunc($event)">产品列表弹出层</ProductSelect>

    <!--选择门店-->
    <StoreSelect :isstore="isstore" :islist="isstorelist"></StoreSelect>
  </div>
</template>

<script>
import { deepClone } from '@/utils/base.js';
import Setpages from './params/Setpages.vue';
import Banner from './params/Banner.vue';
import ImageSingle from './params/ImageSingle.vue';
import Window from './params/Window.vue';
import Video from './params/Video.vue';
import Notice from './params/Notice.vue';
import NavBar from './params/NavBar.vue';
import Product from './params/Product.vue';
import Store from './params/Store.vue';
import RichText from './params/RichText.vue';
import Blank from './params/Blank.vue';
import Guide from './params/Guide.vue';
import Upload from '@/components/file/Upload';
import ProductSelect from '@/components/product/Product';
import StoreSelect from '@/components/store/StoreSelect';

export default {
  components: {
    /*顶部设置*/
    Setpages,
    /*图片轮播组件*/
    Banner,
    /*图片组件*/
    ImageSingle,
    /*图片橱窗*/
    Window,
    /*视频*/
    Video,
    /*公告组*/
    Notice,
    /*导航组*/
    NavBar,
    /*商品组*/
    Product,
    /*门店*/
    Store,
    /*富文本*/
    RichText,
    /*辅助空白*/
    Blank,
    /*辅助线*/
    Guide,
    /*上传图片*/
    Upload,
    /*商品选择*/
    ProductSelect,
    /*门店选中*/
    StoreSelect
  },
  data() {
    return {
      /*是否上传图片*/
      isupload: false,
      /*图片当前对象*/
      imgModel: null,
      /*是否打开产品弹出层*/
      isproduct: false,
      /*商品需要去重的*/
      excludeIds: [],
      /*是否多选*/
      islist: false,
      /*是否显示门店选中*/
      isstore: false,
      /*门店是否多选*/
      isstorelist: false
    };
  },
  props: ['form', 'defaultData', 'diyData', 'opts'],
  created() {},
  methods: {
    /**
     * 编辑器：添加data元素
     */
    onEditorAddData: function() {
      let self = this;
      // 新增data数据
      var newDataItem = deepClone(self.defaultData[self.form.curItem.type].data[0]);
      self.form.curItem.data.push(newDataItem);
    },
    /**
     * 编辑器：重置颜色
     * @param holder
     * @param attribute
     * @param color
     */
    onEditorResetColor: function(holder, attribute, color) {
      holder[attribute].titleBackgroundColor = color;
    },
    /**
     * 编辑器：删除data元素
     * @param index
     * @param selectedIndex
     */
    onEditorDeleleData: function(index, selectedIndex) {
      let self = this;
      if (self.diyData.items[selectedIndex].data.length <= 1) {
        self.$message({
          message: '至少保留一个',
          type: 'error'
        });
        return false;
      }
      self.diyData.items[selectedIndex].data.splice(index, 1);
    },
    /**
     * 编辑器：选择图片
     * @param source
     * @param index
     */
    onEditorSelectImage: function(index, imgUrl) {
      this.isupload = true;
      this.imgModel = {
        index: index,
        imgUrl: imgUrl
      };
    },

    /*上传图片*/
    returnImgsFunc(e) {
      if (e != null) {
        this.imgModel.index[this.imgModel.imgUrl] = e[0]['file_path'];
      }
      this.isupload = false;
    },

    /*商品选择列表弹出层*/
    openProduct(list, islist) {
      let arr = [];
      list.forEach(item => {
        arr.push(item.product_id);
      });
      this.excludeIds = arr;
      if (islist && typeof islist != 'undefined') {
        this.islist = true;
      } else {
        this.islist = false;
      }
      this.isproduct = true;
    },

    /*商品选择关闭弹窗*/
    closeProductDialogFunc(e) {
      if (this.form.curItem == null) {
        return;
      }
      this.isproduct = false;
      if (e.type == 'success') {
        if (this.islist) {
          this.form.curItem.data = this.form.curItem.data.concat(e.params);
        } else {
          this.form.curItem.data.push(e.params);
        }
      }
    },

    /*商品选择列表弹出层*/
    openStore(list, islist) {
      let arr = [];
      list.forEach(item => {
        arr.push(item.store_id);
      });
      this.excludeIds = arr;
      if (islist && typeof islist != 'undefined') {
        this.isstorelist = true;
      } else {
        this.isstorelist = false;
      }
      this.isstore = true;
    }
  }
};
</script>

<style>
.param-img-item {
  position: relative;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #eeeeee;
  line-height: 20px;
}
.param-img-item .delete-box {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
  color: #cccccc;
}
.param-img-item .delete-box:hover {
  color: rgb(255, 51, 0);
}
.param-img-item .pic img {
  width: 200px;
  height: 100px;
  margin: 0 auto;
}
.param-img-item .icon img {
  width: 100px;
  height: 100px;
  margin: 0 auto;
}
.param-img-item .url-box {
  display: flex;
  justify-content: flex-start;
  line-height: 40px;
}
.param-img-item .url-box .key-name {
  display: block;
  width: 80px;
}
.param-img-item .url-box .el-input {
  flex: 1;
}
</style>
