<?php

namespace app\shop\model\order;

use app\common\model\order\Order as OrderModel;
use app\common\library\helper;
use app\common\enum\order\OrderTypeEnum;
use app\common\service\message\MessageService;
use app\common\service\order\OrderRefundService;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\service\product\factory\ProductFactory;
use app\common\model\plus\coupon\UserCoupon as UserCouponModel;
use app\common\model\user\User as UserModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\shop\service\order\ExportService;
use app\common\model\settings\Setting as SettingModel;

/**
 * 订单模型
 */
class Order extends OrderModel
{
    /**
     * 订单列表
     */
    public function getList($dataType, $data = null)
    {
        $model = $this;
        // 检索查询条件
        $model = $model->setWhere($model, $data);
        // 获取数据列表
        return $model->with(['product' => ['image'], 'user', 'supplier'])
            ->order(['create_time' => 'desc'])
            ->where($this->transferDataType($dataType))
            ->paginate($data, false, [
                'query' => \request()->request()
            ]);
    }

    /**
     * 获取订单总数
     */
    public function getCount($type = 'all', $data)
    {
        // 筛选条件
        $filter = ['shop_supplier_id' => $data['shop_supplier_id']];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order_status'] = 10;
                break;
            case 'process';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'complete';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['order_status'] = 30;
                break;
        }
        return $this->where('order_type', '=', $data['order_type'])->where($filter)->count();
    }

    /**
     * 订单列表(全部)
     */
    public function getListAll($dataType, $query = [])
    {
        $model = $this;
        // 检索查询条件
        $model = $model->setWhere($model, $query);
        // 获取数据列表
        return $model->with(['product.image', 'address', 'user', 'extract'])
            ->alias('order')
            ->field('order.*')
            ->where($this->transferDataType($dataType))
            ->where('order.is_delete', '=', 0)
            ->order(['order.create_time' => 'desc'])
            ->select();
    }

    /**
     * 订单导出
     */
    public function exportList($dataType, $query)
    {
        // 获取订单列表
        $list = $this->getListAll($dataType, $query);
        // 导出excel文件
        return (new Exportservice)->orderList($list);
    }

    /**
     * 设置检索查询条件
     */
    private function setWhere($model, $data)
    {
        //搜索订单号
        if (isset($data['order_no']) && $data['order_no'] != '') {
            $model = $model->where('order_no', 'like', '%' . trim($data['order_no']) . '%');
        }
        //搜索配送方式
        if (isset($data['style_id']) && $data['style_id'] != '') {
            $model = $model->where('delivery_type', '=', $data['style_id']);
        }
        //搜索配送方式
        if (isset($data['order_type'])) {
            $model = $model->where('order_type', '=', $data['order_type']);
        }
        //搜索配送方式
        if (isset($data['shop_supplier_id']) && $data['shop_supplier_id']) {
            $model = $model->where('shop_supplier_id', '=', $data['shop_supplier_id']);
        }
        //搜索时间段
        if (isset($data['create_time']) && $data['create_time'] != '') {
            $sta_time = array_shift($data['create_time']);
            $end_time = array_pop($data['create_time']);
            $model = $model->whereBetweenTime('create_time', $sta_time, $end_time);
        }
        return $model;
    }

    /**
     * 转义数据类型条件
     */
    private function transferDataType($dataType)
    {
        $filter = [];
        // 订单数据类型
        switch ($dataType) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order_status'] = 10;
                break;
            case 'process';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'complete';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['order_status'] = 30;
                break;
        }
        return $filter;
    }

    /**
     * 确认发货(单独订单)
     */
    public function delivery($data)
    {
        // 转义为订单列表
        $orderList = [$this];
        // 验证订单是否满足发货条件
        if (!$this->verifyDelivery($orderList)) {
            return false;
        }
        // 整理更新的数据
        $updateList = [[
            'order_id' => $this['order_id'],
            'express_id' => $data['express_id'],
            'express_no' => $data['express_no']
        ]];
        // 更新订单发货状态
        if ($status = $this->updateToDelivery($updateList)) {
            // 获取已发货的订单
            $completed = self::detail($this['order_id'], ['user', 'address', 'product', 'express']);
            // 发送消息通知
            $this->sendDeliveryMessage([$completed]);
        }
        return $status;
    }

    /**
     * 确认发货后发送消息通知
     */
    private function sendDeliveryMessage($orderList)
    {
        // 实例化消息通知服务类
        $Service = new MessageService;
        foreach ($orderList as $item) {
            // 发送消息通知
            $Service->delivery($item, OrderTypeEnum::MASTER);
        }
        return true;
    }

    /**
     * 发送订单
     * @return bool
     */
    public function sendOrder($order_id)
    {
        $deliver = SettingModel::getSupplierItem('deliver', $this['supplier']['shop_supplier_id']);
        if ($this['order_status']['value'] != 10 || $this['deliver_status'] != 0) {
            $this->error = '订单已发送或已完成';
            return false;
        }
        // 开启事务
        $this->startTrans();
        try {
            $this->addOrder($deliver);
            // 实例化消息通知服务类
            $Service = new MessageService;
            // 发送消息通知
            $Service->delivery($this, OrderTypeEnum::MASTER);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 更新订单发货状态(批量)
     */
    private function updateToDelivery($orderList)
    {
        $data = [];
        foreach ($orderList as $item) {
            $data[] = [
                'order_id' => $item['order_id'],
                'express_no' => $item['express_no'],
                'express_id' => $item['express_id'],
                'delivery_status' => 20,
                'delivery_time' => time(),
            ];
        }
        return $this->saveAll($data);
    }

    /**
     * 验证订单是否满足发货条件
     */
    private function verifyDelivery($orderList)
    {
        foreach ($orderList as $order) {
            if (
                $order['pay_status']['value'] != 20
                || $order['delivery_type']['value'] != DeliveryTypeEnum::EXPRESS
                || $order['delivery_status']['value'] != 10
            ) {
                $this->error = "订单号[{$order['order_no']}] 不满足发货条件!";
                return false;
            }
        }
        return true;
    }

    /**
     * 修改订单价格
     */
    public function updatePrice($data)
    {
        if ($this['pay_status']['value'] != 10) {
            $this->error = '该订单不合法';
            return false;
        }
        if ($this['order_source'] != 10) {
            $this->error = '该订单不合法';
            return false;
        }
        // 实际付款金额
        $payPrice = bcadd($data['update_price'], $data['update_express_price'], 2);
        if ($payPrice <= 0) {
            $this->error = '订单实付款价格不能为0.00元';
            return false;
        }
        return $this->save([
                'order_no' => $this->orderNo(), // 修改订单号, 否则微信支付提示重复
                'order_price' => $data['update_price'],
                'pay_price' => $payPrice,
                'update_price' => helper::bcsub($data['update_price'], helper::bcsub($this['total_price'], $this['coupon_money'])),
                'express_price' => $data['update_express_price']
            ]) !== false;
    }

    /**
     * 审核：用户取消订单
     */
    public function confirmCancel($data)
    {
        // 判断订单是否有效
        if ($this['pay_status']['value'] != 20 || $this['order_status']['value'] != 10) {
            $this->error = '该订单不合法';
            return false;
        }
        // 订单取消事件
        $status = $this->transaction(function () use ($data) {
            // 执行退款操作
            (new OrderRefundService)->execute($this);
            // 回退商品库存
            ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], true);
            // 回退用户优惠券
            $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
            // 回退用户积分
            $user = UserModel::detail($this['user_id']);
            $describe = "订单取消：{$this['order_no']}";
            $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe);
            // 实例化消息通知服务类
            $Service = new MessageService;
            // 发送消息通知
            $Service->cancel($this, $data['cancel_remark']);
            // 更新订单状态
            return $this->save(['order_status' => 20, 'cancel_remark' => $data['cancel_remark']]);
        });
        return $status;
    }

    /**
     * 审核：用户取消订单
     */
    public function refund($data)
    {
        // 判断订单是否有效
        if ($this['pay_status']['value'] != 20 || $this['order_status']['value'] != 10) {
            $this->error = '该订单不合法';
            return false;
        }
        if ($data['refund_money'] + $this['refund_money'] > $this['pay_price']) {
            $this->error = '退款金额不能超过支付金额';
            return false;
        }
        // 订单取消事件
        $status = $this->transaction(function () use ($data) {
            // 执行退款操作
            (new OrderRefundService)->execute($this, $data['refund_money']);
            // 更新订单状态
            return $this->save(['refund_money' => $this['refund_money'] + $data['refund_money']]);
        });
        return $status;
    }

    /**
     * 获取待处理订单
     */
    public function getReviewOrderTotal($shop_supplier_id = 0)
    {
        $model = $this;
        $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
        $filter['delivery_status'] = 10;
        $filter['order_status'] = 10;
        if ($shop_supplier_id) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        return $model->where($filter)->count();
    }

    /**
     * 获取某天的总销售额
     * 结束时间不传则查一天
     */
    public function getOrderTotalPrice($startDate = null, $endDate = null, $shop_supplier_id = 0)
    {
        $model = $this;
        $startDate && $model = $model->where('pay_time', '>=', strtotime($startDate));
        if (is_null($endDate) && $startDate) {
            $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else if ($endDate) {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }
        if ($shop_supplier_id) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        return $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('is_delete', '=', 0)
            ->sum('pay_price');
    }

    /**
     * 获取某天的客单价
     * 结束时间不传则查一天
     */
    public function getOrderPerPrice($startDate = null, $endDate = null)
    {
        $model = $this;
        $model = $model->where('pay_time', '>=', strtotime($startDate));
        if (is_null($endDate)) {
            $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }
        return $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('is_delete', '=', 0)
            ->avg('pay_price');
    }

    /**
     * 获取某天的下单用户数
     */
    public function getPayOrderUserTotal($day, $shop_supplier_id = 0)
    {
        $model = $this;
        $startTime = strtotime($day);
        if ($shop_supplier_id) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        $userIds = $model->distinct(true)
            ->where('pay_time', '>=', $startTime)
            ->where('pay_time', '<', $startTime + 86400)
            ->where('pay_status', '=', 20)
            ->where('is_delete', '=', 0)
            ->column('user_id');
        return count($userIds);
    }

    /**
     * 获取平台的总销售额
     */
    public function getTotalMoney($type = 'all', $is_settled = -1)
    {
        $model = $this;
        $model = $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('is_delete', '=', 0);
        if ($is_settled == 0) {
            $model = $model->where('is_settled', '=', 0);
        }
        if ($type == 'all') {
            return $model->sum('pay_price');
        } else if ($type == 'supplier') {
            return ($model->sum('pay_price')) - ($model->sum('refund_money'));
        } else if ($type == 'sys') {
            return $model->sum('sys_money');
        }
        return 0;
    }


}