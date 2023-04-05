<?php

namespace app\api\controller\order;

use app\api\controller\Controller;
use app\api\model\order\Cart as CartModel;
use app\api\model\supplier\Supplier as SupplierModel;

/**
 * 购物车控制器
 */
class Cart extends Controller
{
    private $user;

    // $model
    private $model;

    /**
     * 构造方法
     */
    public function initialize()
    {
        $this->user = $this->getUser();
        $this->model = new CartModel;
    }


    /**
     * 购物车列表
     */
    public function lists()
    {
        // 请求参数
        $param = $this->request->param();
        // 购物车商品列表
        $productList = $this->model->getList($param, $this->user);
        // 购物车商品总数量
        $cart_total_num = $this->model->getProductNum($param, $this->user);
        // 购物车商品价格
        $total_product_price = $this->model->getPrice($param, $this->user, 'price');
        // 购物车商品总包装费
        $total_bag_price = $this->model->getPrice($param, $this->user, 'bag_price');
        //门店信息
        $supplier = SupplierModel::detail($param['shop_supplier_id']);
        //购物车总价
        $total_price = $total_product_price;
        $supplier['bag_type'] == 0 && $total_price = $total_price + $total_bag_price;
        //最低消费差额
        $min_money_diff = $supplier['min_money'] - $total_price;
        $min_money_diff = $supplier['min_money'] && $min_money_diff > 0 ? $min_money_diff : 0;
        return $this->renderSuccess('', compact('productList', 'cart_total_num', 'total_price', 'total_bag_price', 'total_product_price', 'min_money_diff'));
    }

    /**
     * 加入购物车
     * @param int $product_id 商品id
     * @param int $product_num 商品数量
     */
    public function add()
    {
        $data = $this->request->param();
        $model = $this->model;
        if (!$model->add($data, $this->user)) {
            return $this->renderError($model->getError() ?: '加入购物车失败');
        }
        // 购物车商品总数量
        $totalNum = $model->getProductNum($data, $this->user);
        return $this->renderSuccess('加入购物车成功', ['cart_total_num' => $totalNum]);
    }

    /**
     * 商品列表加减数量
     * @param $product_id
     */
    public function productSub($product_id)
    {
        $model = $this->model::detail(['product_id' => $product_id, 'user_id' => $this->getUser()['user_id']]);
        if ($model && $model->sub($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失败');

    }

    /**
     * 加减购物商品数量
     * @param $cart_id
     */
    public function sub($cart_id)
    {
        $model = $this->model::detail($cart_id);
        if ($model && $model->sub($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失败');

    }

    /**
     * 清空购物车
     * @return array
     */
    public function delete()
    {
        $this->model->deleteAll($this->postData(), $this->user);
        return $this->renderSuccess('删除成功');
    }
}