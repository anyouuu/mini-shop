<?php

namespace app\api\model\order;

use app\common\model\order\Cart as CartModel;
use app\api\model\supplier\Supplier as SupplierModel;
use app\api\model\product\Product as ProductModel;

/**
 * 普通订单模型
 */
class Cart extends CartModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'app_id',
        'update_time'
    ];

    /**
     * 购物车列表 (含商品信息)
     */
    public function getList($data, $user)
    {
        // 获取购物车商品列表
        $model = $this;
        $list = $model->with(['product', 'image.file'])
            ->where('shop_supplier_id', '=', $data['shop_supplier_id'])
            ->where('cart_type', '=', $data['cart_type'])
            ->where('user_id', '=', $user['user_id'])
            ->select();
        return $list;
    }

    /**
     * 购物车列表 (含商品信息)
     */
    public function getCartList($data, $user)
    {
        // 获取购物车商品列表
        $model = $this;
        $list = $model->with(['product', 'sku', 'image.file'])
            ->field("*,(price*product_num) as total_price,(bag_price*product_num) as total_bag_price")
            ->where('cart_id', 'in', $data['cart_ids'])
            ->where('shop_supplier_id', '=', $data['shop_supplier_id'])
            ->where('user_id', '=', $user['user_id'])
            ->select();
        return $list;
    }

    /**
     * 加入购物车
     */
    public function add($data, $user)
    {
        //判断是否营业
        $business = (new SupplierModel)->supplierState($data['shop_supplier_id'], $data['dinner_type']);
        if (!$business) {
            return false;
        }
        //判断商品是否下架
        $product = $this->productState($data['product_id']);
        if (!$product) {
            $this->error = '商品已下架';
            return false;
        }
        //判断是否存在
        $cart_id = $this->isExist($data, $user);
        if ($cart_id) {
            return $this->where('cart_id', '=', $cart_id)->inc('product_num', $data['product_num'])->update();
        } else {
            $data['describe'] = trim($data['describe'], ';');
            $data['user_id'] = $user['user_id'];
            $data['app_id'] = self::$app_id;
            return $this->save($data);
        }

    }

    /**
     * 判断购物车商品是否存在
     */
    public function isExist($data, $user)
    {
        $cart_id = $this->where('user_id', '=', $user['user_id'])
            ->where('product_id', '=', $data['product_id'])
            ->where('shop_supplier_id', '=', $data['shop_supplier_id'])
            ->where('product_sku_id', '=', $data['product_sku_id'])
            ->where('feed', '=', $data['feed'])
            ->where('attr', '=', $data['attr'])
            ->value('cart_id');
        return $cart_id;
    }

    /**
     * 加减商品
     */
    public function sub($param)
    {
        //判断是否营业
        $business = (new SupplierModel)->supplierState($this['shop_supplier_id'], $param['dinner_type']);
        if (!$business) {
            return false;
        }
        //判断商品是否下架
        $product = $this->productState($this['product_id']);
        if (!$product) {
            $this->error = '商品已下架';
            return false;
        }
        if ($param['type'] == 'down') {
            if ($this['product_num'] <= 1) {
                return $this->delete();
            }
            return $this->where('cart_id', '=', $this['cart_id'])->dec('product_num', 1)->update();
        } else {
            return $this->where('cart_id', '=', $this['cart_id'])->inc('product_num', 1)->update();
        }
    }

    /**
     *清空购物车
     */
    public function deleteAll($param, $user)
    {
        return $this->where('shop_supplier_id', '=', $param['shop_supplier_id'])
            ->where('cart_type', '=', $param['cart_type'])
            ->where('user_id', '=', $user['user_id'])
            ->delete();
    }

    /**
     * 获取当前用户购物车商品总数量(含件数)
     */
    public function getProductNum($param, $user)
    {
        $count = $this->where('shop_supplier_id', '=', $param['shop_supplier_id'])
            ->where('cart_type', '=', $param['cart_type'])
            ->where('user_id', '=', $user['user_id'])
            ->sum('product_num');
        return $count ? $count : 0;
    }

    //获取购物车单个商品数量
    public function getSingleProductNum($product_id, $user)
    {
        $num = $this->where('product_id', '=', $product_id)
            ->where('user_id', '=', $user['user_id'])
            ->sum('product_num');
        return $num ? $num : 0;
    }

    //获取购物车价格
    public function getPrice($param, $user, $field)
    {
        $price = $this->where('shop_supplier_id', '=', $param['shop_supplier_id'])
            ->where('user_id', '=', $user['user_id'])
            ->where('cart_type', '=', $param['cart_type'])
            ->sum("$field*product_num");
        return $price ? $price : 0;
    }

    //更新购物车
    public function clearAll($car_ids)
    {
        return $this->where('cart_id', 'in', $car_ids)->delete();
    }

    //判断商品是否下架
    public function productState($product_id)
    {
        return (new ProductModel)->where('product_id', '=', $product_id)
            ->where('product_status', '=', 10)
            ->where('is_delete', '=', 0)
            ->count();
    }

}