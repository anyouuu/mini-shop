webpackJsonp([6],{"9Py+":function(e,t){},"T/6T":function(e,t,l){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=l("vLgD"),o={messageList:function(e,t){return a.a._post("/admin/message/index",e,t)},addMessage:function(e,t){return a.a._post("/admin/message/add",e,t)},editMessage:function(e,t){return a.a._post("/admin/message/edit",e,t)},deleteMessage:function(e,t){return a.a._post("/admin/message/delete",e,t)},fieldList:function(e,t){return a.a._post("/admin/message/field",e,t)},saveField:function(e,t){return a.a._post("/admin/message/saveField",e,t)}},s={data:function(){return{categoryList:[],formLabelWidth:"120px",dialogVisible:!1,loading:!1}},props:["open_edit","form"],created:function(){this.dialogVisible=this.open_edit},methods:{editMessage:function(){var e=this,t=this.form;e.$refs.form.validate(function(l){l&&(e.loading=!0,o.editMessage(t,!0).then(function(t){e.loading=!1,e.$message({message:"恭喜你，修改成功",type:"success"}),e.dialogFormVisible(!0)}).catch(function(t){e.loading=!1}))})},dialogFormVisible:function(e){e?this.$emit("closeDialog",{type:"success",openDialog:!1}):this.$emit("closeDialog",{type:"error",openDialog:!1})}}},i={render:function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("el-dialog",{attrs:{title:"编辑消息",visible:e.dialogVisible,"close-on-click-modal":!1,"close-on-press-escape":!1},on:{"update:visible":function(t){e.dialogVisible=t},close:e.dialogFormVisible}},[l("el-form",{ref:"form",attrs:{size:"small",model:e.form}},[l("el-form-item",{attrs:{label:"消息名称","label-width":e.formLabelWidth,rules:[{required:!0,message:" "}],prop:"message_name"}},[l("el-input",{attrs:{autocomplete:"off",placeholder:"请输入消息名称"},model:{value:e.form.message_name,callback:function(t){e.$set(e.form,"message_name",t)},expression:"form.message_name"}})],1),e._v(" "),l("el-form-item",{attrs:{label:"名称(英文唯一)","label-width":e.formLabelWidth,rules:[{required:!0,message:" "}],prop:"message_ename"}},[l("el-input",{attrs:{autocomplete:"off",placeholder:"请输入消息英文名称"},model:{value:e.form.message_ename,callback:function(t){e.$set(e.form,"message_ename",t)},expression:"form.message_ename"}})],1),e._v(" "),l("el-form-item",{attrs:{label:"通知对象","label-width":e.formLabelWidth}},[l("el-select",{attrs:{placeholder:"请选择通知对象"},model:{value:e.form.message_to,callback:function(t){e.$set(e.form,"message_to",t)},expression:"form.message_to"}},[l("el-option",{attrs:{label:"会员",value:10}}),e._v(" "),l("el-option",{attrs:{label:"商家",value:20}}),e._v(" "),l("el-option",{attrs:{label:"供应商",value:30}})],1)],1),e._v(" "),l("el-form-item",{attrs:{label:"消息类别","label-width":e.formLabelWidth}},[l("el-select",{attrs:{placeholder:"请选择消息类别"},model:{value:e.form.message_type,callback:function(t){e.$set(e.form,"message_type",t)},expression:"form.message_type"}},[l("el-option",{attrs:{label:"订单",value:10}}),e._v(" "),l("el-option",{attrs:{label:"分销",value:20}}),e._v(" "),l("el-option",{attrs:{label:"通知",value:30}})],1)],1),e._v(" "),l("el-form-item",{attrs:{label:"排序","label-width":e.formLabelWidth}},[l("el-input",{attrs:{placeholder:"请输入数字"},model:{value:e.form.sort,callback:function(t){e.$set(e.form,"sort",t)},expression:"form.sort"}})],1),e._v(" "),l("el-form-item",{attrs:{label:"备注","label-width":e.formLabelWidth}},[l("el-input",{attrs:{autocomplete:"off",placeholder:"请输入备注"},model:{value:e.form.remark,callback:function(t){e.$set(e.form,"remark",t)},expression:"form.remark"}})],1)],1),e._v(" "),l("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[l("el-button",{on:{click:e.dialogFormVisible}},[e._v("取 消")]),e._v(" "),l("el-button",{attrs:{type:"primary",loading:e.loading},on:{click:e.editMessage}},[e._v("确 定")])],1)],1)},staticRenderFns:[]};var n=l("VU/8")(s,i,!1,function(e){l("fBzz")},null,null).exports,r={data:function(){return{form:{status:0,sort:100},categoryList:[],sort:"100",radio:"1",formLabelWidth:"120px",dialogVisible:!1,loading:!1}},props:["open_add"],created:function(){this.dialogVisible=this.open_add},methods:{addClick:function(){var e=this,t=this.form;e.$refs.form.validate(function(l){l&&(e.loading=!0,o.addMessage(t,!0).then(function(t){1==t.code?(e.loading=!1,e.$message({message:"恭喜你，添加成功",type:"success"}),e.dialogFormVisible(!0)):e.loading=!1}).catch(function(e){}))})},dialogFormVisible:function(e){e?this.$emit("closeDialog",{type:"success",openDialog:!1}):this.$emit("closeDialog",{type:"error",openDialog:!1})}}},d={render:function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("el-dialog",{attrs:{title:"添加消息",visible:e.dialogVisible,"close-on-click-modal":!1,"close-on-press-escape":!1},on:{"update:visible":function(t){e.dialogVisible=t},close:e.dialogFormVisible}},[l("el-form",{ref:"form",attrs:{size:"small",model:e.form}},[l("el-form-item",{attrs:{label:"消息名称","label-width":e.formLabelWidth,rules:[{required:!0,message:" "}],prop:"message_name"}},[l("el-input",{attrs:{autocomplete:"off",placeholder:"请输入消息名称"},model:{value:e.form.message_name,callback:function(t){e.$set(e.form,"message_name",t)},expression:"form.message_name"}})],1),e._v(" "),l("el-form-item",{attrs:{label:"名称(英文唯一)","label-width":e.formLabelWidth,rules:[{required:!0,message:" "}],prop:"message_ename"}},[l("el-input",{attrs:{autocomplete:"off",placeholder:"请输入消息英文名称"},model:{value:e.form.message_ename,callback:function(t){e.$set(e.form,"message_ename",t)},expression:"form.message_ename"}})],1),e._v(" "),l("el-form-item",{attrs:{label:"通知对象","label-width":e.formLabelWidth}},[l("el-select",{attrs:{placeholder:"请选择通知对象"},model:{value:e.form.message_to,callback:function(t){e.$set(e.form,"message_to",t)},expression:"form.message_to"}},[l("el-option",{attrs:{label:"会员",value:10}}),e._v(" "),l("el-option",{attrs:{label:"商家",value:20}}),e._v(" "),l("el-option",{attrs:{label:"供应商",value:30}})],1)],1),e._v(" "),l("el-form-item",{attrs:{label:"消息类别","label-width":e.formLabelWidth}},[l("el-select",{attrs:{placeholder:"请选择消息类别"},model:{value:e.form.message_type,callback:function(t){e.$set(e.form,"message_type",t)},expression:"form.message_type"}},[l("el-option",{attrs:{label:"订单",value:10}}),e._v(" "),l("el-option",{attrs:{label:"分销",value:20}}),e._v(" "),l("el-option",{attrs:{label:"通知",value:30}})],1)],1),e._v(" "),l("el-form-item",{attrs:{label:"排序","label-width":e.formLabelWidth}},[l("el-input",{attrs:{placeholder:"请输入数字"},model:{value:e.form.sort,callback:function(t){e.$set(e.form,"sort",t)},expression:"form.sort"}})],1),e._v(" "),l("el-form-item",{attrs:{label:"备注","label-width":e.formLabelWidth}},[l("el-input",{attrs:{autocomplete:"off",placeholder:"请输入备注"},model:{value:e.form.remark,callback:function(t){e.$set(e.form,"remark",t)},expression:"form.remark"}})],1)],1),e._v(" "),l("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[l("el-button",{on:{click:e.dialogFormVisible}},[e._v("取 消")]),e._v(" "),l("el-button",{attrs:{type:"primary",loading:e.loading},on:{click:function(t){return e.addClick()}}},[e._v("确 定")])],1)],1)},staticRenderFns:[]};var c=l("VU/8")(r,d,!1,function(e){l("9Py+")},null,null).exports,m={data:function(){return{formLabelWidth:"120px",dialogVisible:!1,title:"字段管理",fieldData:[],deleteIds:[],loading:!0}},props:["open_field","form"],created:function(){this.dialogVisible=this.open_field,this.title=this.title+"("+this.form.message_name+")",this.getFieldList()},methods:{getFieldList:function(){var e=this;o.fieldList({message_id:e.form.message_id},!0).then(function(t){e.loading=!1,e.fieldData=t.data.list}).catch(function(t){e.loading=!1})},saveField:function(){var e=this;o.saveField({message_id:e.form.message_id,fieldData:e.fieldData,deleteIds:e.deleteIds},!0).then(function(t){e.$message({message:"恭喜你，修改成功",type:"success"}),e.dialogFormVisible(!0)}).catch(function(e){})},addClick:function(){this.fieldData.push({message_field_id:0,message_id:this.form.message_id,field_name:"",field_ename:"",filed_value:"",sort:100})},deleteClick:function(e){var t=this.fieldData[e];t.message_field_id>0&&this.deleteIds.push(t.message_field_id),this.fieldData.splice(e,1)},checkRow:function(e,t){t.is_var=e?1:0},dialogFormVisible:function(e){e?this.$emit("closeDialog",{type:"success",openDialog:!1}):this.$emit("closeDialog",{type:"error",openDialog:!1})}}},f={render:function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("el-dialog",{attrs:{title:e.title,visible:e.dialogVisible,"close-on-click-modal":!1,"close-on-press-escape":!1},on:{"update:visible":function(t){e.dialogVisible=t},close:e.dialogFormVisible}},[l("div",{staticClass:"common-level-rail"},[l("el-button",{attrs:{size:"small",type:"primary"},on:{click:e.addClick}},[e._v("添加字段")])],1),e._v(" "),l("el-form",{attrs:{size:"small"}},[l("div",{staticClass:"table-wrap"},[l("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],attrs:{border:"",data:e.fieldData}},[l("el-table-column",{attrs:{label:"字段名称",width:"120px"},scopedSlots:e._u([{key:"default",fn:function(t){return[l("el-input",{attrs:{size:"small",prop:"field_name"},model:{value:t.row.field_name,callback:function(l){e.$set(t.row,"field_name",l)},expression:"scope.row.field_name"}})]}}])}),e._v(" "),l("el-table-column",{attrs:{label:"字段英文名",width:"130px"},scopedSlots:e._u([{key:"default",fn:function(t){return[l("el-input",{attrs:{size:"small",prop:"field_ename"},model:{value:t.row.field_ename,callback:function(l){e.$set(t.row,"field_ename",l)},expression:"scope.row.field_ename"}})]}}])}),e._v(" "),l("el-table-column",{attrs:{label:"字段默认值"},scopedSlots:e._u([{key:"default",fn:function(t){return[l("el-input",{attrs:{size:"small",prop:"filed_value"},model:{value:t.row.filed_value,callback:function(l){e.$set(t.row,"filed_value",l)},expression:"scope.row.filed_value"}})]}}])}),e._v(" "),l("el-table-column",{attrs:{label:"是否变量",width:"70px"},scopedSlots:e._u([{key:"default",fn:function(t){return[l("el-checkbox",{attrs:{prop:"is_var",checked:1===t.row.is_var},on:{change:function(l){return e.checkRow(l,t.row)}}})]}}])}),e._v(" "),l("el-table-column",{attrs:{label:"排序",width:"70px"},scopedSlots:e._u([{key:"default",fn:function(t){return[l("el-input",{attrs:{size:"small",prop:"sort"},model:{value:t.row.sort,callback:function(l){e.$set(t.row,"sort",l)},expression:"scope.row.sort"}})]}}])}),e._v(" "),l("el-table-column",{attrs:{fixed:"right",label:"操作",width:"70px"},scopedSlots:e._u([{key:"default",fn:function(t){return[l("el-button",{attrs:{type:"text",size:"small"},on:{click:function(l){return e.deleteClick(t.$index)}}},[e._v("删除")])]}}])})],1)],1)]),e._v(" "),l("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[l("el-button",{on:{click:e.dialogFormVisible}},[e._v("取 消")]),e._v(" "),l("el-button",{attrs:{type:"primary",loading:e.loading},on:{click:e.saveField}},[e._v("确 定")])],1)],1)},staticRenderFns:[]};var u=l("VU/8")(m,f,!1,function(e){l("prDw")},null,null).exports,p=l("aFVK"),g={components:{Edit:n,Add:c,Field:u},data:function(){return{loading:!0,activeIndex:"0",formInline:{user:"",region:""},tableData:[],totalDataNumber:0,categoryList:[],open_add:!1,open_edit:!1,messageModel:{},open_field:!1}},created:function(){this.getTableList()},methods:{getTableList:function(){var e=this;o.messageList({},!0).then(function(t){e.loading=!1,e.tableData=t.data.list,e.totalDataNumber=t.data.list.total}).catch(function(t){e.loading=!1})},addClick:function(){this.open_add=!0},editClick:function(e){this.messageModel=Object(p.a)(e),this.messageModel.message_to=this.messageModel.message_to.value,this.messageModel.message_type=this.messageModel.message_type.value,this.open_edit=!0},fieldClick:function(e){this.messageModel=e,this.open_field=!0},closeDialogFunc:function(e,t){"add"==t&&(this.open_add=e.openDialog,"success"==e.type&&this.getTableList()),"edit"==t&&(this.open_edit=e.openDialog,"success"==e.type&&this.getTableList()),"field"==t&&(this.open_field=e.openDialog,"success"==e.type&&this.getTableList())},deleteClick:function(e){var t=this;t.$confirm("删除后不可恢复，确认删除该记录吗?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){t.loading=!0,o.deleteMessage({message_id:e.message_id},!0).then(function(e){t.loading=!1,t.$message({message:e.msg,type:"success"}),t.getTableList()}).catch(function(e){t.loading=!1})}).catch(function(){})}}},_={render:function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("div",{staticClass:"product"},[l("div",{staticClass:"common-level-rail"},[l("el-button",{attrs:{size:"small",type:"primary"},on:{click:e.addClick}},[e._v("添加消息")])],1),e._v(" "),l("div",{staticClass:"product-content"},[l("div",{staticClass:"table-wrap"},[l("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],attrs:{size:"small",data:e.tableData,border:"","default-expand-all":"","tree-props":{children:"children"},"row-key":"plus_id"}},[l("el-table-column",{attrs:{prop:"message_name",label:"消息名称"}}),e._v(" "),l("el-table-column",{attrs:{prop:"message_ename",label:"消息名称(英文)"}}),e._v(" "),l("el-table-column",{attrs:{prop:"message_to",label:"通知对象"}}),e._v(" "),l("el-table-column",{attrs:{prop:"message_type",label:"消息类型"}}),e._v(" "),l("el-table-column",{attrs:{label:"字段"},scopedSlots:e._u([{key:"default",fn:function(t){return[l("el-button",{attrs:{type:"text",size:"small"},on:{click:function(l){return e.fieldClick(t.row)}}},[e._v("字段管理")])]}}])}),e._v(" "),l("el-table-column",{attrs:{prop:"remark",label:"消息描述"}}),e._v(" "),l("el-table-column",{attrs:{prop:"sort",label:"排序"}}),e._v(" "),l("el-table-column",{attrs:{prop:"create_time",label:"添加时间"}}),e._v(" "),l("el-table-column",{attrs:{fixed:"right",label:"操作"},scopedSlots:e._u([{key:"default",fn:function(t){return[l("el-button",{attrs:{type:"text",size:"small"},on:{click:function(l){return e.editClick(t.row)}}},[e._v("编辑")]),e._v(" "),l("el-button",{attrs:{type:"text",size:"small"},on:{click:function(l){return e.deleteClick(t.row)}}},[e._v("删除")])]}}])})],1)],1)]),e._v(" "),e.open_add?l("Add",{attrs:{open_add:e.open_add},on:{closeDialog:function(t){return e.closeDialogFunc(t,"add")}}}):e._e(),e._v(" "),e.open_edit?l("Edit",{attrs:{open_edit:e.open_edit,form:e.messageModel},on:{closeDialog:function(t){return e.closeDialogFunc(t,"edit")}}}):e._e(),e._v(" "),e.open_field?l("Field",{attrs:{open_field:e.open_field,form:e.messageModel},on:{closeDialog:function(t){return e.closeDialogFunc(t,"field")}}}):e._e()],1)},staticRenderFns:[]};var b=l("VU/8")(g,_,!1,function(e){l("reD0")},null,null);t.default=b.exports},fBzz:function(e,t){},prDw:function(e,t){},reD0:function(e,t){}});
//# sourceMappingURL=6.d12c93f3c02cefe9ab21.js.map