<?php

namespace app\api\service\order;

use app\common\library\alipay\AliPay;
use app\common\library\easywechat\AppOpen;
use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\AppMp;
use app\common\library\easywechat\WxPay;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayTypeEnum;

class PaymentService
{
    /**
     * 构建订单支付参数
     */
    public static function orderPayment($user, $order, $payType)
    {
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::wechat(
                $user,
                $order['order_id'],
                $order['order_no'],
                $order['pay_price'],
                OrderTypeEnum::MASTER
            );
        }
        return [];
    }

    /**
     * 构建微信支付
     */
    public static function wechat(
        $user,
        $order,
        $orderType = OrderTypeEnum::MASTER,
        $pay_source
    )
    {
        // 统一下单API
        if ($pay_source == 'wx') {
            $app = AppWx::getWxPayApp($user['app_id']);
            $open_id = $user['open_id'];
        } else if ($pay_source == 'mp') {
            $app = AppMp::getWxPayApp($user['app_id']);
            $open_id = $user['mpopen_id'];
        } else if ($pay_source == 'payH5') {
            $app = AppMp::getWxPayApp($user['app_id']);
            $open_id = '';
        } else if ($pay_source == 'app') {
            $app = AppOpen::getWxPayApp($user['app_id']);
            $open_id = $user['appopen_id'];
        }
        $orderNo = $order['order_no'];
        $payPrice = $order['pay_price'];

        $WxPay = new WxPay($app);
        $payment = $WxPay->unifiedorder($orderNo, $open_id, $payPrice, $orderType, $pay_source);
        if ($pay_source == 'wx') {
            return $payment;
        } else if ($pay_source == 'mp') {
            $jssdk = $app->jssdk;
            return $jssdk->bridgeConfig($payment['prepay_id']);
        } else if ($pay_source == 'payH5') {
            return $payment;
        } else if ($pay_source == 'app') {
            return $payment;
        }
    }


    /**
     * 构建支付宝支付
     */
    public static function alipay(
        $user,
        $order,
        $orderType = OrderTypeEnum::MASTER,
        $pay_source
    )
    {
        $orderNo = $order['order_no'];
        $payPrice = $order['pay_price'];

        $AliPay = new AliPay($pay_source);
        $payment = $AliPay->unifiedorder($orderNo, $payPrice, $orderType, $pay_source);
        return $payment;
    }
}