<template>
  <!--
    	作者：luoyiming
    	时间：2020-06-20
    	描述：diy组件-参数设置-轮播图
    -->
  <div>
    <div class="common-form">
      <span>{{ curItem.name }}</span>
    </div>
    <el-form size="small" :model="curItem" label-width="100px">
      <el-form-item label="指示点颜色：">
        <div class="d-s-c">
          <el-color-picker v-model="curItem.style.btnColor"></el-color-picker>
          <el-button type="button" style="margin-left: 10px;"
            @click.stop="$parent.onEditorResetColor(curItem.style, 'btnColor', '#ffffff')">重置
          </el-button>
        </div>
      </el-form-item>
      <el-form-item label="切换时间：">
        <el-input v-model="curItem.params.interval"></el-input>
        <p>轮播图自动切换的间隔时间，单位：毫秒</p>
      </el-form-item>
      <el-form-item label="图片：">
        <div class="param-img-item" :key="index" v-for="(banner, index) in curItem.data">
          <div class="delete-box"><i class="el-icon-delete-solid"
              @click="$parent.onEditorDeleleData(index, selectedIndex)"></i></div>
          <div class="pic">
            <img v-img-url="banner.imgUrl" alt="" @click="$parent.onEditorSelectImage(banner, 'imgUrl')" />
          </div>
          <p class="tc gray9">建议尺寸750x360</p>
          <div class="d-s-c">
            <div class="url-box flex-1">
              <span class="key-name">链接地址：</span>
              <el-input placeholder="请选择链接地址" v-model="banner.name"></el-input>
            </div>
            <div class="url-box ml10">
              <el-button type="primary" @click="changeLink(index,'banner')">选择链接</el-button>
            </div>
          </div>
        </div>
        <div class="d-c-c">
          <el-button @click="$parent.onEditorAddData">添加一个</el-button>
        </div>
      </el-form-item>
      <el-form-item label="图片：">
        <div class="param-img-item" :key="index" v-for="(navBar, index) in curItem.params.nav">
          <div class="icon">
            <img v-img-url="navBar.navimgUrl" alt="" @click="$parent.onEditorSelectImage(navBar, 'navimgUrl')">
          </div>
          <p class="tc gray9">建议尺寸100x100</p>
          <div class="url-box">
            <span class="key-name">标题内容：</span>
            <el-input v-model="navBar.title"></el-input>
          </div>
          <div class="url-box">
            <span class="key-name">文字内容：</span>
            <el-input v-model="navBar.text"></el-input>
          </div>
          <div class="d-s-c">
            <div class="url-box flex-1">
              <span class="key-name">链接名称：</span>
              <el-input v-model="navBar.navlinkUrl"></el-input>
            </div>
            <div class="url-box ml10">
              <el-button type="primary" @click="changeLink(index,'nav')">选择链接</el-button>
            </div>
          </div>
        </div>
      </el-form-item>
    </el-form>
    <Setlink v-if="is_linkset" :is_linkset="is_linkset" @closeDialog="closeLinkset">选择链接</Setlink>
  </div>
</template>

<script>
  import Setlink from '@/components/setlink/Setlink';
  export default {
    components: {
      Setlink
    },
    data() {
      return {
        /*是否选择链接*/
        is_linkset: false,
        index: null,
        linktype: ''
      };
    },
    props: ['curItem', 'selectedIndex'],
    methods: {
      /*选择链接*/
      changeLink(index, type) {
        this.is_linkset = true;
        this.index = index;
        this.linktype = type;
      },

      /*获取链接并关闭弹窗*/
      closeLinkset(e) {
        //                console.log(e);
        this.is_linkset = false;
        if (this.linktype == 'banner') {
          this.curItem.data[this.index].linkUrl = e.url;
          this.curItem.data[this.index].name = '链接到' + ' ' + e.type + ' ' + e.name;
        } else if (this.linktype == 'nav') {
          this.curItem.params.nav[this.index].navlinkUrl = e.url;
        }

      }
    }
  };
</script>

<style></style>
