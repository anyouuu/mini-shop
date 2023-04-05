import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)
let menu = [
  {
      path: '/login',
      name: 'login',
      meta: {
        title: '登录'
      },
      component: () =>
        import('@/views/Login')
    },
    {
      path: '/test',
      name: 'Test',
      meta: {
        title: '测试'
      },
      component: () =>
        import('@/views/help/Test')
    },
    {
      path: '/fonticon',
      name: 'Fonticon',
      meta: {
        title: '字体图标'
      },
      component: () =>
        import('@/views/help/Fonticon')
    },
    {
      path: '*',
      name: 'Page404',
      meta: {
        title: '错误页面'
      },
      component: () =>
        import('@/views/error-page/404')
    }
]


const state = {
  getters: {
    roles: null,
    menulist: null
  }

}
const mutations = {
  saveMenulist(state, value) {
    state.getters.menulist = value;
  },

  saveState(state, value) {
    state.getters.roles = value;
  }

}

const actions = {
  UpdateRole(context, value) {
    context.commit("saveState", value)
  },
  generateRoutes: async function(context, str) {
    /*返回理由*/
    return new Promise((resolve, reject) => {
      /*判断本地缓存是否有菜单数据*/
        if(res.status&&res.data){
        	let temps=null;
        	let accessRoutes=temps.concat(menu);
        	context.commit("saveMenulist",menulisttemp);
        	context.commit("saveState",str);
        	resolve(accessRoutes);
        }else{
        	context.commit("saveState",null);
        	resolve([]);
        }

    })
  }
}


export default new Vuex.Store({
  state,
  actions,
  mutations
})
