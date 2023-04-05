import getBaseInfoList from './getBaseInfo.js'
import { setCookie,getCookie } from '@/utils/base';
/*全局状态*/
const common = {

  namespaced: true,

  /*状态值*/
  state: {
    is_show: false,
    page: null,
    base_info: null
  },

  /*状态值转换*/
  getters: {
    getTset: (state) => (name) => {
      return state.test + name;
    }
  },

  /*改变状态的方法 不可异步*/
  mutations: {
    setState(state, value) {
      state[value.key] = value.val;
    }

  },

  /*可异步改变*/
  actions: {
    saveTest(context) {
      context.commit('setState')
    },

    getBaseInfo(context) {
      return new Promise((resolve, reject)=>{
        let _data=getCookie('baseInfo');
        if(_data!=null){
          context.commit('setState',{key:'baseInfo',val:JSON.parse(_data)});
          resolve(JSON.parse(_data));
        }else{
          getBaseInfoList().then(res=>{
            context.commit('setState',{key:'baseInfo',val:res});
            setCookie('baseInfo',JSON.stringify(res));
            resolve(res);
          }).catch(error => {
            reject(error);
          });
        }
      })

    }
  }
}
export default common;
