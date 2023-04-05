import request from '@/utils/request'

let SupplierApi = {
  /*供应商列表*/
  supplierList(data, errorback) {
    return request._post('/shop/supplier.supplier/index', data, errorback);
  },
  /*供应商编辑*/
  toEditSupplier(data, errorback) {
    return request._get('/shop/supplier.supplier/edit', data, errorback);
  },
  /*供应商设置*/
  setSupplier(data, errorback) {
    return request._post('/shop/supplier.supplier/setting', data, errorback);
  },
  /*供应商设置*/
  getsetSupplier(data, errorback) {
    return request._get('/shop/supplier.supplier/setting', data, errorback);
  },
  /*供应商编辑*/
  editSupplier(data, errorback) {
    return request._post('/shop/supplier.supplier/edit', data, errorback);
  },
  /*提现记录*/
  cashList(data, errorback) {
    return request._post('/shop/supplier.supplier/cash', data, errorback);
  },
  /*提现审核*/
  cashSubmit(data, errorback) {
    return request._post('/shop/supplier.cash/submit', data, errorback);
  },
  /*提现确认打款*/
  cashMoney(data, errorback) {
    return request._post('/shop/supplier.supplier/money', data, errorback);
  },
  /*开启禁止*/
  supplierRecycle(data, errorback) {
    return request._post('/shop/supplier.supplier/recycle', data, errorback);
  },
  /*店铺明细*/
  Capital(data, errorback) {
    return request._post('/shop/supplier.supplier/balance', data, errorback);
  },
}
export default SupplierApi;
