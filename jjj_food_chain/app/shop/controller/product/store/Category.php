<?php

namespace app\shop\controller\product\store;

use app\shop\controller\Controller;
use app\shop\model\product\Category as CategoryModel;

/**
 * 商品分类
 */
class Category extends Controller
{
    /**
     * 普通商品分类列表
     */
    public function index()
    {
        $model = new CategoryModel;
        $list = $model->getCacheTree(1);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 特殊商品分类列表
     */
    public function list()
    {
        $model = new CategoryModel;
        $list = $model->getCacheTree(1, 1);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 删除商品分类
     */
    public function delete($category_id)
    {
        $model = CategoryModel::find($category_id);
        if ($model->remove($category_id)) {
            return $this->renderSuccess('删除成功');
        }
        return $this->renderError($model->getError() ?: '删除失败');
    }

    /**
     * 添加商品分类
     */
    public function add()
    {
        $model = new CategoryModel;
        $data = $this->request->post();
        $data['type'] = 1;
        // 新增记录
        if ($model->add($data)) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑商品分类
     */
    public function edit($category_id)
    {
        // 模板详情
        $model = CategoryModel::detail($category_id);
        // 更新记录
        if ($model->edit($this->request->post())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 得到修改图片
     * @return array
     */
    public function image($category_id)
    {
        $model = new CategoryModel;
        $detail = $model->detailWithImage(['category_id' => $category_id]);
        return $this->renderSuccess('', compact('detail'));
    }


}
