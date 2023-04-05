<?php

namespace app\shop\service;

use app\shop\model\product\Product;
use app\shop\model\order\Order;
use app\shop\model\user\User;
use app\shop\model\supplier\Supplier as SupplierModel;
/**
 * 商城模型
 */
class ShopService
{
    // 商品模型
    private $ProductModel;
    // 订单模型
    private $OrderModel;
    // 用户模型
    private $UserModel;

    /**
     * 构造方法
     */
    public function __construct()
    {
        /* 初始化模型 */
        $this->ProductModel = new Product();
        $this->OrderModel = new Order();
        $this->UserModel = new User();
    }

    /**
     * 后台首页数据
     */
    public function getHomeData($user)
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        // 最近七天日期
        $lately7days = $this->getLately7days();
        $where = [];
        $shop_supplier_id = 0;
        if ($user['user_type'] == 1) {
            $where = ['shop_supplier_id' => $user['shop_supplier_id']];
            $shop_supplier_id = $user['shop_supplier_id'];
        }
        $data = [
            'top_data' => [
                // 商品总量
                'product_total' => $this->getProductTotal($where),
                // 用户总量
                'user_total' => $this->getUserTotal(),
                // 订单总量
                'order_total' => $this->getOrderTotal(null, $shop_supplier_id),
                // 店铺总量
                'supplier_total' => $this->getSupplierTotal(),
                // 营业额
                'total_money' => $this->getOrderTotalPrice(null, $shop_supplier_id),
                // 预计收入
                'income_money' => $this->getOrderIncome(null, $shop_supplier_id),
            ],
            'wait_data' => [
                //订单
                'order' => [
                    'disposal' => $this->getReviewOrderTotal($shop_supplier_id),
                ],
                //库存
                'stock' => [
                    'product' => $this->getProductStockTotal($shop_supplier_id),
                ],
            ],
            'today_data' => [
                // 销售额(元)
                'order_total_price' => [
                    'tday' => $this->getOrderTotalPrice($today, $shop_supplier_id),
                    'ytd' => $this->getOrderTotalPrice($yesterday, $shop_supplier_id)
                ],
                // 支付订单数
                'order_total' => [
                    'tday' => $this->getOrderTotal($today, $shop_supplier_id),
                    'ytd' => $this->getOrderTotal($yesterday, $shop_supplier_id)
                ],
                // 新增用户数
                'new_user_total' => [
                    'tday' => $this->getUserTotal($today),
                    'ytd' => $this->getUserTotal($yesterday)
                ],
                // 新供应商数
                'new_supplier_total' => [
                    'tday' => SupplierModel::getSupplierTotalByDay($today),
                    'ytd' => SupplierModel::getSupplierTotalByDay($yesterday)
                ],
                // 下单用户数
                'order_user_total' => [
                    'tday' => $this->getPayOrderUserTotal($today, $shop_supplier_id),
                    'ytd' => $this->getPayOrderUserTotal($yesterday, $shop_supplier_id)
                ],
                // 预计收入(元)
                'income_money' => [
                    'tday' => $this->getOrderIncome($today, $shop_supplier_id),
                    'ytd' => $this->getOrderIncome($yesterday, $shop_supplier_id)
                ],
            ],
            'product_data' => [
                // 销量排行
                'salesNumRank' => $this->OrderModel->getProductRank(0, -1, $shop_supplier_id),
                // 销售额排行
                'salesMoneyRank' => $this->OrderModel->getProductRank(1, -1, $shop_supplier_id),
            ],
        ];
        return $data;
    }

    /**
     * 最近七天日期
     */
    private function getLately7days()
    {
        // 获取当前周几
        $date = [];
        for ($i = 0; $i < 7; $i++) {
            $date[] = date('Y-m-d', strtotime('-' . $i . ' days'));
        }
        return array_reverse($date);
    }

    /**
     * 获取预计收入
     */
    private function getOrderIncome($day = null, $shop_supplier_id = 0)
    {
        return number_format($this->OrderModel->getOrderData($day, null, 'income_price', $shop_supplier_id));
    }

    /**
     * 获取商品总量
     */
    private function getProductTotal($where = [])
    {
        return number_format($this->ProductModel->getProductTotal($where));
    }

    /**
     * 获取商品库存告急总量
     */
    private function getProductStockTotal($shop_supplier_id)
    {
        return number_format($this->ProductModel->getProductStockTotal($shop_supplier_id));
    }

    /**
     * 获取用户总量
     */
    private function getUserTotal($day = null)
    {
        return number_format($this->UserModel->getUserTotal($day));
    }

    /**
     * 获取订单总量
     */
    private function getOrderTotal($day = null, $shop_supplier_id = 0)
    {
        return number_format($this->OrderModel->getOrderData($day, null, 'order_total', $shop_supplier_id));
    }

    /**
     * 获取待处理订单总量
     */
    private function getReviewOrderTotal($shop_supplier_id)
    {
        return number_format($this->OrderModel->getReviewOrderTotal($shop_supplier_id));
    }


    /**
     * 获取供应商总量
     */
    private function getSupplierTotal()
    {
        $model = new SupplierModel;
        return number_format($model->getSupplierTotal());
    }

    /**
     * 获取某天的总销售额
     */
    private function getOrderTotalPrice($day = null, $shop_supplier_id = 0)
    {
        return sprintf('%.2f', $this->OrderModel->getOrderTotalPrice($day, null, $shop_supplier_id));
    }

    /**
     * 获取某天的下单用户数
     */
    private function getPayOrderUserTotal($day, $shop_supplier_id = 0)
    {
        return number_format($this->OrderModel->getPayOrderUserTotal($day, $shop_supplier_id));
    }

    /**
     * 商品数据
     */
    public function getProductData($data)
    {

        $data = [
            // 商品总量
            'product_total' => $this->getProductTotal(['shop_supplier_id' => $data['shop_supplier_id'], 'product_type' => $data['product_type']]),
            // 上架商品总量
            'up_total' => $this->getProductTotal(['shop_supplier_id' => $data['shop_supplier_id'], 'product_type' => $data['product_type'], 'product_status' => 10]),
            // 下架商品总量
            'down_total' => $this->getProductTotal(['shop_supplier_id' => $data['shop_supplier_id'], 'product_type' => $data['product_type'], 'product_status' => 20]),
        ];
        return $data;
    }

    /**
     * 商品数据
     */
    public function getProductRank($data, $type)
    {

        return $this->ProductModel->getProductRank($data, $type);
    }

}