<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\common\enum\order\OrderPayTypeEnum;

/**
 * 我的订单
 */
class Order extends Controller
{
    // user
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUser();   // 用户信息

    }

    /**
     * 我的订单列表
     */
    public function lists()
    {
        $data = $this->postData();
        $model = new OrderModel;
        $list = $model->getList($this->user['user_id'], $data);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 订单详情信息
     */
    public function detail($order_id)
    {
        // 订单详情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 剩余支付时间
        if ($model['pay_status']['value'] == 10 && $model['order_status']['value'] != 20) {
            $model['pay_end_time'] = $this->formatPayEndTime($model['pay_end_time'] - time());
        } else {
            $model['pay_end_time'] = '';
        }
        return $this->renderSuccess('', [
            'order' => $model,  // 订单详情
            'setting' => [
                // 积分名称
                //'points_name' => SettingModel::getPointsName(),
            ]
        ]);
    }

    /**
     * 支付成功详情信息
     */
    public function paySuccess($order_id)
    {
        $order_arr = explode(',', $order_id);
        $order = [
            'pay_price' => 0,
            'points_bonus' => 0
        ];
        foreach ($order_arr as $id) {
            $model = OrderModel::getUserOrderDetail($id, $this->user['user_id']);
            $order['pay_price'] += $model['pay_price'];
            $order['points_bonus'] += $model['points_bonus'];
        }
        return $this->renderSuccess('', compact('order'));
    }

    /**
     * 取消订单
     */
    public function cancel($order_id)
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if ($model->cancel($this->user)) {
            return $this->renderSuccess('订单取消成功');
        }
        return $this->renderError($model->getError() ?: '订单取消失败');
    }

    /**
     * 立即支付
     */
    public function pay($order_id, $payType = OrderPayTypeEnum::WECHAT, $pay_source = 'wx')
    {
        // 获取订单详情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 订单支付事件
        if (!$model->onPay($payType)) {
            return $this->renderError($model->getError() ?: '订单支付失败');
        }
        // 构建微信支付请求
        $payment = $model->onOrderPayment($this->user, [$model], $payType, $pay_source);
        // 支付状态提醒
        return $this->renderSuccess('', [
            'order_id' => $model['order_id'],   // 订单id
            'pay_type' => $payType,             // 支付方式
            'payment' => $payment               // 微信支付参数
        ], ['success' => '支付成功', 'error' => '订单未支付']);
    }

    private function formatPayEndTime($leftTime)
    {
        if ($leftTime <= 0) {
            return '';
        }
        $str = '';
        $day = floor($leftTime / 86400);
        $hour = floor(($leftTime - $day * 86400) / 3600);
        $min = floor((($leftTime - $day * 86400) - $hour * 3600) / 60);

        if ($day > 0) $str .= $day . '天';
        if ($hour > 0) $str .= $hour . '小时';
        if ($min > 0) $str .= $min . '分钟';
        return $str;
    }
}