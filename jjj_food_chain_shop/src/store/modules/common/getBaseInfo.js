import AuthApi from '@/api/auth.js';
/*获取权限*/
const baseInfo =async function(){
  return new Promise((resolve, reject) => {
      AuthApi.getUserInfo({}, true)
        .then(res => {
          resolve(res.data);
        })
        .catch(error => {
          reject(error);
        });
   });
}

export default baseInfo;
