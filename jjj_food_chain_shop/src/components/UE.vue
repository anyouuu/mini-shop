<template>
  <div>
    <script id="editor" type="text/plain"></script>
    <Upload v-if="isupload" :config="{total:9}" :isupload="isupload" @returnImgs="returnImgsFunc">上传图片</Upload>
  </div>
</template>
<script>
  import Upload from '@/components/file/Upload';
  export default {
    components:{Upload},
    name: 'ue',
    data() {
      return {
        editor: null,
        isupload:false,
        hasCallback: false,
        callback: null,
        this_config:{
          //不需要工具栏漂浮
          autoFloatEnabled:false
        }
      }
    },
    props: {
      text: String,
      config: Object
    },
    created() {
      /*富文本框的默认选择图片方式，改成自定义的选择方式*/
      window.openUpload = this.openUpload;
    },
    watch:{

    },
    mounted() {

      Object.assign(this.this_config,this.config);
      this.editor = window.UE.getEditor('editor', this.this_config);
      this.editor.addListener('ready', (e) => {
        this.editor.setContent(this.text);
      });

      /*监听富文本内容变化，有变化传给调用组件的页面*/
      this.editor.addListener('contentChange', (e) => {
        this.$emit('contentChange',this.editor.getContent());
      });
    },
    methods: {

      /*获取富文本框内容*/
      getUEContent() {
        return this.editor.getContent()
      },

      /*打开选择图片*/
      openUpload: function(callback) {
        this.isupload = true;
        if (callback) {
          this.hasCallback = true;
          this.callback = callback;
        }
      },

      /*获取图片*/
      returnImgsFunc(e) {
        if (e != null) {
          this.hasCallback && this.callback(e);
        }
        this.isupload = false;
      },
    },

    /*销毁*/
    destroyed() {
      this.editor.destroy()
    }
  }
</script>
