<?php

namespace app\shop\controller\takeout;

use app\shop\controller\Controller;
use app\shop\model\order\Order as OrderModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\model\settings\Setting as SettingModel;

/**
 * 订单控制器
 */
class Order extends Controller
{
    /**
     * 订单列表
     */
    public function index($dataType = 'all')
    {
        // 订单列表
        $model = new OrderModel();
        $data = $this->postData();
        $data['order_type'] = 0;
        $data['shop_supplier_id'] = $this->store['user']['shop_supplier_id'];
        $list = $model->getList($dataType, $data);
        $order_count = [
            'order_count' => [
                'payment' => $model->getCount('payment', $data),
                'process' => $model->getCount('process', $data),
                'complete' => $model->getCount('complete', $data),
            ],];
        $ex_style = DeliveryTypeEnum::deliver();
        //配送方式
        $deliver = SettingModel::getSupplierItem('deliver', $this->store['user']['shop_supplier_id']);
        $deliver_name = $deliver['engine'][$deliver['default']]['name'];
        return $this->renderSuccess('', compact('list', 'ex_style', 'order_count', 'deliver_name'));
    }

    /**
     * 订单详情
     */
    public function detail($order_id)
    {
        // 订单详情
        $detail = OrderModel::detail($order_id);
        if (isset($detail['pay_time']) && $detail['pay_time'] != '') {
            $detail['pay_time'] = date('Y-m-d H:i:s', $detail['pay_time']);
        }
        if (isset($detail['delivery_time']) && $detail['delivery_time'] != '') {
            $detail['delivery_time'] = date('Y-m-d H:i:s', $detail['delivery_time']);
        }
        $detail['buy_remark'] = json_decode($detail['buy_remark'], 1);
        return $this->renderSuccess('', compact('detail'));
    }
}