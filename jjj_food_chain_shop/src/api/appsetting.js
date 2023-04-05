import request from '@/utils/request'

let AppSettingApi = {
  /*小程序*/
  appwxDetail(data, errorback) {
    return request._get('/shop/appsetting.appwx/index', data, errorback);
  },
  /*小程序*/
  editAppWx(data, errorback) {
    return request._post('/shop/appsetting.appwx/index', data, errorback);
  },
}

export default AppSettingApi;
