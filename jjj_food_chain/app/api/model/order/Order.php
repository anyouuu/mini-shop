<?php

namespace app\api\model\order;

use app\api\service\order\paysuccess\type\MasterPaySuccessService;
use app\api\service\order\PaymentService;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderSourceEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\enum\order\OrderStatusEnum;
use app\common\exception\BaseException;
use app\common\model\order\Order as OrderModel;
use app\api\service\order\checkpay\CheckPayFactory;
use app\common\service\product\factory\ProductFactory;
use app\common\model\plus\coupon\UserCoupon as UserCouponModel;

/**
 * 普通订单模型
 */
class Order extends OrderModel
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
     * 订单支付事件
     */
    public function onPay($payType = OrderPayTypeEnum::WECHAT)
    {
        // 判断订单状态
        $checkPay = CheckPayFactory::getFactory($this['order_source']);

        if (!$checkPay->checkOrderStatus($this)) {
            $this->error = $checkPay->getError();
            return false;
        }

        // 余额支付
        if ($payType == OrderPayTypeEnum::BALANCE) {
            return $this->onPaymentByBalance($this['order_no']);
        }
        return true;
    }

    /**
     * 用户中心订单列表
     */
    public function getList($user_id, $params)
    {
        $model = $this;
        if (isset($params['shop_supplier_id']) && $params['shop_supplier_id']) {
            $model = $model->where('shop_supplier_id', '=', $params['shop_supplier_id']);
        }
        if (isset($params['order_type'])) {
            $model = $model->where('order_type', '=', $params['order_type']);
        }
        if (isset($params['delivery_type']) && $params['delivery_type']) {
            $model = $model->where('delivery_type', '=', $params['delivery_type']);
        }
        switch ($params['dataType']) {
            case '0':
                break;
            case '1'://进行中
                $model = $model->where('pay_status', '=', 20)->where('order_status', '=', 10);
                break;
            case '2'://已完成
                $model = $model->where('pay_status', '=', 20)->where('order_status', '=', 30);
                break;
        }
        return $model->with(['product.image', 'supplier'])
            ->where('user_id', '=', $user_id)
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate($params);
    }

    /**
     * 取消订单
     */
    public function cancel($user)
    {
        if ($this['delivery_status']['value'] == 20) {
            $this->error = '已发货订单不可取消';
            return false;
        }
        // 订单取消事件
        return $this->transaction(function () use ($user) {
            // 订单是否已支付
            $isPay = $this['pay_status']['value'] == OrderPayStatusEnum::SUCCESS;
            // 未付款的订单
            if ($isPay == false) {
                //主商品退回库存
                ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], $isPay);
                // 回退用户优惠券
                $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
                // 回退用户积分
                $describe = "订单取消：{$this['order_no']}";
                $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe);
            }
            // 更新订单状态
            return $this->save(['order_status' => $isPay ? OrderStatusEnum::APPLY_CANCEL : OrderStatusEnum::CANCELLED]);
        });
    }

    /**
     * 订单详情
     */
    public static function getUserOrderDetail($order_id, $user_id)
    {
        $model = new static();
        $order = $model->where(['order_id' => $order_id, 'user_id' => $user_id,])->with(['product' => ['image'], 'address', 'supplier','user','deliver'])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        return $order;
    }

    /**
     * 余额支付标记订单已支付
     */
    public function onPaymentByBalance($orderNo)
    {
        // 获取订单详情
        $PaySuccess = new MasterPaySuccessService($orderNo);
        // 发起余额支付
        $status = $PaySuccess->onPaySuccess(OrderPayTypeEnum::BALANCE);
        if (!$status) {
            $this->error = $PaySuccess->getError();
        }
        return $status;
    }

    /**
     * 构建微信支付请求
     */
    protected static function onPaymentByWechat($user, $order, $pay_source)
    {
        return PaymentService::wechat(
            $user,
            $order,
            OrderTypeEnum::MASTER,
            $pay_source
        );
    }

    /**
     * 构建支付宝请求
     */
    protected static function onPaymentByAlipay($user, $order_arr, $pay_source)
    {
        return PaymentService::alipay(
            $user,
            $order_arr,
            OrderTypeEnum::MASTER,
            $pay_source
        );
    }

    /**
     * 待支付订单详情
     */
    public static function getPayDetail($orderNo)
    {
        $model = new static();
        return $model->where(['order_no' => $orderNo, 'pay_status' => 10, 'is_delete' => 0])->with(['product', 'user'])->find();
    }

    /**
     * 构建支付请求的参数
     */
    public static function onOrderPayment($user, $order, $payType, $pay_source)
    {
        //如果来源是h5,首次不处理，payH5再处理
        if ($pay_source == 'h5') {
            return [];
        }
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::onPaymentByWechat($user, $order, $pay_source);
        }
        if ($payType == OrderPayTypeEnum::ALIPAY) {
            return self::onPaymentByAlipay($user, $order, $pay_source);
        }
        return [];
    }

    /**
     * 设置错误信息
     */
    protected function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在错误
     */
    public function hasError()
    {
        return !empty($this->error);
    }

    /**
     * 主订单购买的数量
     * 未取消的订单
     */
    public static function getHasBuyOrderNum($user_id, $product_id)
    {
        $model = new static();
        return $model->alias('order')->where('order.user_id', '=', $user_id)
            ->join('order_product', 'order_product.order_id = order.order_id', 'left')
            ->where('order_product.product_id', '=', $product_id)
            ->where('order.order_source', '=', OrderSourceEnum::MASTER)
            ->where('order.order_status', '<>', 21)
            ->sum('total_num');
    }
}