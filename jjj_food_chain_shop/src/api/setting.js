import request from '@/utils/request'

let SettingApi = {

  /*商城设置模板变量*/
  storeDetail(data, errorback) {
    return request._get('/shop/setting.store/index', data, errorback);
  },
  /*保存商城设置*/
  editStore(data, errorback) {
    return request._post('/shop/setting.store/index', data, errorback);
  },
  /*交易设置模板变量*/
  tradeDetail(data, errorback) {
    return request._get('/shop/setting.trade/index', data, errorback);
  },
  /*保存交易设置*/
  editTrade(data, errorback) {
    return request._post('/shop/setting.trade/index', data, errorback);
  },
  /*短信通知模板变量*/
  smsDetail(data, errorback) {
    return request._get('/shop/setting.sms/index', data, errorback);
  },
  /*保存短信通知*/
  editSms(data, errorback) {
    return request._post('/shop/setting.sms/index', data, errorback);
  },
  /*发送短信*/
  sendSms(data, errorback) {
    return request._post('/shop/setting.sms/smsTest', data, errorback);
  },
  /*上传设置模板变量*/
  storageDetail(data, errorback) {
    return request._get('/shop/setting.storage/index', data, errorback);
  },
  /*保存上传设置*/
  editStorage(data, errorback) {
    return request._post('/shop/setting.storage/index', data, errorback);
  },
  /*打印设置模板变量*/
  printingDetail(data, errorback) {
    return request._get('/shop/setting.printing/index', data, errorback);
  },
  /*保存打印设置*/
  editPrinting(data, errorback) {
    return request._post('/shop/setting.printing/index', data, errorback);
  },
  /*打印设置模板变量*/
  clearDetail(data, errorback) {
    return request._get('/shop/setting.clear/index', data, errorback);
  },
  /*保存缓存*/
  editCache(data, errorback) {
    return request._post('/shop/setting.clear/index', data, errorback);
  },
  /*打印机列表*/
  printerList(data, errorback) {
    return request._post('/shop/setting.printer/index', data, errorback);
  },
  /*打印机类型*/
  printerType(data, errorback) {
    return request._get('/shop/setting.printer/add', data, errorback);
  },
  /*添加打印机*/
  addPrinter(data, errorback) {
    return request._post('/shop/setting.printer/add', data, errorback);
  },
  /*打印机详情*/
  printerDetail(data, errorback) {
    return request._get('/shop/setting.printer/edit', data, errorback);
  },
  /*修改打印机*/
  editPrinter(data, errorback) {
    return request._post('/shop/setting.printer/edit', data, errorback);
  },
  /*删除打印机*/
  deletePrinter(data, errorback) {
    return request._post('/shop/setting.printer/delete', data, errorback);
  },
  /*运费模板列表*/
  deliveryList(data, errorback) {
    return request._post('/shop/setting.delivery/index', data, errorback);
  },
  /*配送区域*/
  deliveryArea(data, errorback) {
    return request._post('/shop/setting.delivery/area', data, errorback);
  },
  /*添加运费模板*/
  addDelivery(data, errorback) {
    return request._post('/shop/setting.delivery/add', data, errorback);
  },
  /*运费模板详情*/
  deliveryDetail(data, errorback) {
    return request._get('/shop/setting.delivery/edit', data, errorback);
  },
  /*修改运费模板*/
  editDelivery(data, errorback) {
    return request._post('/shop/setting.delivery/edit', data, errorback);
  },
  /*删除运费模板*/
  deleteDelivery(data, errorback) {
    return request._post('/shop/setting.delivery/delete', data, errorback);
  },
  /*获取手机号设置*/
  getPhoneDetail(data, errorback) {
    return request._get('/shop/setting.message/getphone', data, errorback);
  },
  /*保存获取手机号设置*/
  editGetPhone(data, errorback) {
    return request._post('/shop/setting.message/getphone', data, errorback);
  },
  deliverDetail(data, errorback) {
    return request._get('/shop/setting.deliver/index', data, errorback);
  },
  editDeliver(data, errorback) {
    return request._post('/shop/setting.deliver/index', data, errorback);
  },
}

export default SettingApi;
