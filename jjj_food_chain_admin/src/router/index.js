import Vue from 'vue'
import Router from 'vue-router'

const originalPush = Router.prototype.push
Router.prototype.push = function push(location, onResolve, onReject) {
  if (onResolve || onReject) return originalPush.call(this, location, onResolve, onReject)
  return originalPush.call(this, location).catch(err => err)
}

Vue.use(Router)

let routerlist = [
  /*登录页面*/
  {
    path: '/Login',
    name: 'Login',
    meta: {
      title: '登录'
    },
    component: () =>
      import('@/views/Login')
  },
  /*母版*/
  {
    path: '/',
    redirect: { name: 'Home' },
    meta: {
      title: '母版'
    },
    component: () =>
      import('@/views/layout/Main'),
    children: [
      /*后台首页*/
      {
        path: '/Home',
        name: 'Home',
        meta: {
          title: '首页'
        },
        component: () =>
          import('@/views/home/Home')
      },
    ]
  },
  /*----权限----*/
  {
    path: '/access',
    redirect: { name: 'access_Index' },
    meta: {
      title: '母版'
    },
    component: () =>
      import('@/views/layout/Main'),
    children: [
      /*权限管理*/
      {
        path: 'Index',
        name: 'access_Index',
        meta: {
          title: '后台权限管理'
        },
        component: () =>
          import('@/views/access/Index')
      }
    ]
  },
  /*----商城列表----*/
  {
    path: '/shop',
    redirect: { name: 'shop_Index' },
    meta: {
      title: '母版'
    },
    component: () =>
      import('@/views/layout/Main'),
    children: [
      /*权限管理*/
      {
        path: 'Index',
        name: 'shop_Index',
        meta: {
          title: '权限管理'
        },
        component: () =>
          import('@/views/shop/Index')
      }
    ]
  },
  /*----修改密码----*/
  {
    path: '/password',
    redirect: { name: 'password_Index' },
    meta: {
      title: '母版'
    },
    component: () =>
      import('@/views/layout/Main'),
    children: [
      /*修改密码*/
      {
        path: 'Index',
        name: 'password_Index',
        meta: {
          title: '修改密码'
        },
        component: () =>
          import('@/views/password/Index')
      }
    ]
  },
  /*----消息设置----*/
  {
    path: '/message',
    redirect: { name: 'message_index' },
    meta: {
      title: '母版'
    },
    component: () =>
      import('@/views/layout/Main'),
    children: [
      /*产品管理*/
      {
        path: 'Index',
        name: 'message_index',
        meta: {
          title: '消息设置'
        },
        component: () =>
          import('@/views/message/Index')
      }
    ]
  },
  /*----消息设置----*/
  {
    path: '/setting',
    redirect: { name: 'service_index' },
    meta: {
      title: '母版'
    },
    component: () =>
      import('@/views/layout/Main'),
    children: [
      /*产品管理*/
      {
        path: 'Index',
        name: 'service_index',
        meta: {
          title: '设置'
        },
        component: () =>
          import('@/views/setting/service/index')
      }
    ]
  },
  /*测试页面*/
  {
    path: '/test',
    name: 'Test',
    meta: {
      title: '测试'
    },
    component: () =>
      import('@/views/help/Test')
  },
  /*字体图标查找页面*/
  {
    path: '/fonticon',
    name: 'Fonticon',
    meta: {
      title: '字体图标'
    },
    component: () =>
      import('@/views/help/Fonticon')
  },
  /*错误页面*/
  {
    path: '/error',
    name: 'Page404',
    meta: {
      title: '错误页面'
    },
    component: () =>
      import('@/views/error-page/404')
  }
];

/*创建路由*/
const createRouter = () => new Router({
  //scrollBehavior: () => ({ y: 0 }),
  routes: routerlist
})

const router = createRouter()

export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // the relevant part
}

export default router;
