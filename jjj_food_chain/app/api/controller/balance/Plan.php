<?php

namespace app\api\controller\balance;

use app\api\controller\Controller;
use app\api\model\settings\Setting as SettingModel;
use app\api\model\user\BalancePlan as BalancePlanModel;
use app\api\model\user\BalanceOrder as BalanceOrderModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;

/**
 * 充值套餐
 */
class Plan extends Controller
{
    /**
     * 余额首页
     */
    public function index(){
        $user = $this->getUser();
        $list = (new BalancePlanModel)->getList();
        // 设置
        $settings = SettingModel::getItem('balance');
        return $this->renderSuccess('', compact('list', 'settings'));
    }

    /**
     * 充值套餐
     */
    public function submit($plan_id, $user_money)
    {
        $params = $this->request->param();
        // 用户信息
        $user = $this->getUser();
        // 生成等级订单
        $model = new BalanceOrderModel();
        $order_id = $model->createOrder($user, $plan_id, $user_money);
        if(!$order_id){
            return $this->renderError($model->getError() ?: '购买失败');
        }
        // 在线支付
        $payment = BalanceOrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::WECHAT, $params['pay_source']);
        // 返回结算信息
        return $this->renderSuccess(['success' => '支付成功', 'error' => '订单未支付'], [
            'order_id' => $order_id,   // 订单id
            'pay_type' => OrderPayTypeEnum::WECHAT,  // 支付方式
            'payment' => $payment,               // 微信支付参数
            'order_type' => OrderTypeEnum::BALANCE, //订单类型
        ]);
    }
}