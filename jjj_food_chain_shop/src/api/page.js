import request from '@/utils/request'

let PageApi = {

    /*获取数据*/
    detail(data, errorback) {
        return request._post('/shop/page.page/detail', data, errorback);
    },
    /*上架样式*/
    toAddPage(data, errorback) {
        return request._get('/shop/page.page/add', data, errorback);
    },
    /*上架样式*/
    addPage(data, errorback) {
        return request._post('/shop/page.page/add', data, errorback);
    },
    /*页面列表*/
    PageList(data, errorback) {
        return request._post('/shop/page.page/index', data, errorback);
    },
    /*删除页面*/
    deletePage(data, errorback) {
        return request._post('/shop/page.page/delete', data, errorback);
    },
    /*设为首页*/
    setHome(data, errorback) {
        return request._post('/shop/page.page/setHome', data, errorback);
    },
    /*编辑页面*/
    editHome(data, errorback) {
        return request._get('/shop/page.page/home', data, errorback);
    },
    /*编辑页面*/
    editPage(data, errorback) {
        return request._get('/shop/page.page/edit', data, errorback);
    },
    /*保存编辑页面*/
    SavePage(data, errorback) {
        return request._post('/shop/page.page/edit', data, errorback);
    },
    /*获取分类*/
    getCategory(data, errorback) {
        return request._get('/shop/page.page/category', data, errorback);
    },

    /*提交分类*/
    postCategory(data, errorback) {
        return request._post('/shop/page.page/category', data, errorback);
    },

    /*获取导航*/
    getNav(data, errorback) {
        return request._get('/shop/page.page/nav', data, errorback);
    },
    /*提交导航*/
    postNav(data, errorback) {
        return request._post('/shop/page.page/nav', data, errorback);
    },
    /*获取导航*/
    getbottomNav(data, errorback) {
        return request._get('/shop/page.page/bottomnav', data, errorback);
    },
    /*提交导航*/
    postbottomNav(data, errorback) {
        return request._post('/shop/page.page/bottomnav', data, errorback);
    },

    /*广告列表*/
    menuList(data, errorback) {
      return request._post('/shop/page.page.Mymenu/index', data, errorback);
    },
    /*添加广告*/
    addMenu(data, errorback) {
      return request._post('/shop/page.page.Mymenu/add', data, errorback);
    },
    /*广告详情*/
    menuDetail(data, errorback) {
      return request._get('/shop/page.page.Mymenu/detail', data, errorback);
    },
    /*修改广告*/
    editMenu(data, errorback) {
      return request._post('/shop/page.page.Mymenu/edit', data, errorback);
    },
    /*删除广告*/
    deleteMenu(data, errorback) {
      return request._post('/shop/page.page.Mymenu/delete', data, errorback);
    },
}

export default PageApi;
