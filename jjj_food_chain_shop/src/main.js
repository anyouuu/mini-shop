// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store/'
import directive from './directive'
import filters from '@/filters/index'
import echarts from 'echarts'

import './permission' // permission control

Vue.prototype.$echarts = echarts

Vue.use(ElementUI)

for(let key in filters){
	Vue.filter(key,filters[key]);
}
Vue.prototype.$filter = filters;

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
