<?php

namespace app\common\model\order;

use app\common\enum\order\OrderSourceEnum;
use app\common\model\BaseModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\library\helper;
use app\common\service\order\OrderService;
use app\common\service\order\OrderCompleteService;
use app\common\service\deliveryapi\DadaApi;
use app\common\exception\BaseException;

/**
 * 订单模型模型
 */
class Order extends BaseModel
{
    protected $pk = 'order_id';
    protected $name = 'order';

    /**
     * 追加字段
     * @var string[]
     */
    protected $append = [
        'state_text',
        'order_source_text',
        'order_type_text',
        'deliver_text'
    ];

    /**
     * 订单商品列表
     */
    public function product()
    {
        return $this->hasMany('app\\common\\model\\order\\OrderProduct', 'order_id', 'order_id')->hidden(['content']);
    }


    /**
     * 关联订单收货地址表
     */
    public function address()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderAddress');
    }

    /**
     * 关联自提订单联系方式
     */
    public function extract()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderExtract');
    }

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }

    /**
     * 关联供应商表
     */
    public function supplier()
    {
        return $this->belongsTo('app\\common\\model\\supplier\\Supplier', 'shop_supplier_id', 'shop_supplier_id')->field(['shop_supplier_id', 'name', 'user_id', 'logo', 'city_id', 'region_id', 'link_phone']);
    }

    /**
     * 关联配送信息
     */
    public function deliver()
    {
        return $this->belongsTo('app\\common\\model\\order\\OrderDeliver', 'order_id', 'order_id')->order('deliver_id desc');
    }

    /**
     * 订单状态文字描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getStateTextAttr($value, $data)
    {
        // 订单状态
        if (in_array($data['order_status'], [20, 30])) {
            $orderStatus = [20 => '已取消', 30 => '已完成'];
            return $orderStatus[$data['order_status']];
        }
        // 付款状态
        if ($data['pay_status'] == 10) {
            return '待付款';
        }
        // 发货状态
        if ($data['order_status'] == 10) {
            return '已付款，进行中';
        }
        return $value;
    }

    /**
     * 订单状态文字描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getDeliverTextAttr($value, $data)
    {
        // 订单状态待接单＝1,待取货＝2,配送中＝3,已完成＝4,已取消＝5, 指派单=8
        if (in_array($data['order_status'], [20, 30])) {
            $orderStatus = [20 => '已取消', 30 => '已完成'];
            return $orderStatus[$data['order_status']];
        }
        // 发货状态
        if ($data['delivery_status'] == 10) {
            return '待配送';
        }
        // 发货状态
        if ($data['delivery_status'] == 20) {
            $deliverStatus = [1 => '待接单', 2 => '待取货', 3 => '配送中', 4 => '已完成'];
            return $deliverStatus[$data['deliver_status']];
        }
        return $value;
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getPayTypeAttr($value)
    {
        return ['text' => OrderPayTypeEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 订单类型
     * @param $value
     * @return array
     */
    public function getOrderTypeTextAttr($value, $data)
    {
        return $data['order_type'] == 0 ? '外卖订单' : '店内订单';
    }

    /**
     * 订单来源
     * @param $value
     * @return array
     */
    public function getOrderSourceTextAttr($value, $data)
    {
        return OrderSourceEnum::data()[$data['order_source']]['name'];
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getPayStatusAttr($value)
    {
        return ['text' => OrderPayStatusEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 改价金额（差价）
     * @param $value
     * @return array
     */
    public function getUpdatePriceAttr($value)
    {
        return [
            'symbol' => $value < 0 ? '-' : '+',
            'value' => sprintf('%.2f', abs($value))
        ];
    }

    /**
     * 发货状态
     * @param $value
     * @return array
     */
    public function getDeliveryStatusAttr($value)
    {
        $status = [10 => '待发货', 20 => '已发货'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 收货状态
     * @param $value
     * @return array
     */
    public function getReceiptStatusAttr($value)
    {
        $status = [10 => '待收货', 20 => '已收货'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 收货状态
     * @param $value
     * @return array
     */
    public function getOrderStatusAttr($value)
    {
        $status = [10 => '进行中', 20 => '已取消', 21 => '待取消', 30 => '已完成'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 配送方式
     * @param $value
     * @return array
     */
    public function getDeliveryTypeAttr($value)
    {
        return ['text' => DeliveryTypeEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 发送第三方配送
     * @param $value
     * @return array
     */
    public function addOrder($deliver)
    {
        if ($deliver['default'] == 'local') {
            $this->sendLocal($this);
            return true;
        } else if ($deliver['default'] == 'dada') {
            $is_exist = (new OrderDeliver())->where('order_id', '=', $this['order_id'])
                ->where('status', '=', 20)
                ->where('deliver_source', '=', 20)
                ->find();
            if ($is_exist) {
                $result = (new DadaApi($this['shop_supplier_id']))->reAddOrder($this);
            } else {
                $result = (new DadaApi($this['shop_supplier_id']))->addOrder($this);
            }
            if ($result['status'] == 'fail') {
                throw new BaseException(['msg' => $result['msg']]);
                return false;
            } else {
                $this->save(['deliver_status' => 1, 'deliver_source' => 20, 'delivery_status' => 20]);
                $add = [
                    'deliver_source' => 20,
                    'order_id' => $this['order_id'],
                    'order_no' => $this['order_no'],
                    'distance' => $result['result']['distance'],
                    'price' => $result['result']['fee'],
                    'shop_supplier_id' => $this['shop_supplier_id'],
                    'app_id' => self::$app_id
                ];
                (new OrderDeliver())->save($add);
                return true;
            }
        }
    }

    //商家配送
    public function sendLocal($data)
    {
        $data->save(['deliver_status' => 3, 'deliver_source' => 10, 'delivery_status' => 20]);
        $add = [
            'deliver_source' => 10,
            'order_id' => $data['order_id'],
            'order_no' => $data['order_no'],
            'distance' => $data->getDistance($data['supplier']['longitude'], $data['supplier']['latitude'], $data['address']['longitude'], $data['address']['latitude']),
            'price' => 0,
            'shop_supplier_id' => $data['shop_supplier_id'],
            'deliver_status' => 3,
            'phone' => $data['supplier']['link_phone'],
            'app_id' => self::$app_id
        ];
        (new OrderDeliver())->save($add);
    }

    public static function getDistance($ulon, $ulat, $slon, $slat)
    {
        // 地球半径
        $R = 6378137;
        // 将角度转为狐度
        $radLat1 = deg2rad($ulat);
        $radLat2 = deg2rad($slat);
        $radLng1 = deg2rad($ulon);
        $radLng2 = deg2rad($slon);
        // 结果
        $s = acos(cos($radLat1) * cos($radLat2) * cos($radLng1 - $radLng2) + sin($radLat1) * sin($radLat2)) * $R;
        // 精度
        $s = round($s * 10000) / 10000;
        return round($s);
    }

    /**
     * 订单详情
     * @param $where
     * @param string[] $with
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($where, $with = ['user', 'address', 'product' => ['image'], 'extract', 'supplier'])
    {
        is_array($where) ? $filter = $where : $filter['order_id'] = (int)$where;
        return self::with($with)->where($filter)->find();
    }

    /**
     * 订单详情
     * @param $where
     * @param string[] $with
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detailByNo($order_no, $with = ['user', 'address', 'product' => ['image', 'refund'], 'extract', 'express', 'extractStore.logo', 'extractClerk', 'supplier'])
    {
        return self::with($with)->where('order_no', '=', $order_no)->find();
    }

    /**
     * 批量获取订单列表
     * @param $orderIds
     * @param array $with
     * @return array
     */
    public function getListByIds($orderIds, $with = [])
    {
        $data = $this->getListByInArray('order_id', $orderIds, $with);
        return helper::arrayColumn2Key($data, 'order_id');
    }

    /**
     * 批量更新订单
     * @param $orderIds
     * @param $data
     * @return bool
     */
    public function onBatchUpdate($orderIds, $data)
    {
        return $this->where('order_id', 'in', $orderIds)->save($data);
    }

    /**
     * 批量获取订单列表
     * @param $field
     * @param $data
     * @param array $with
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getListByInArray($field, $data, $with = [])
    {
        return $this->with($with)
            ->where($field, 'in', $data)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 生成订单号
     * @return string
     */
    public function orderNo()
    {
        return OrderService::createOrderNo();
    }

    /**
     * 确认核销（自提订单）
     * @param $extractClerkId
     * @return bool|mixed
     */
    public function verificationOrder()
    {
        if ($this['pay_status']['value'] != 20 || in_array($this['order_status']['value'], [20, 30])) {
            $this->error = '该订单不满足核销条件';
            return false;
        }
        return $this->transaction(function () {
            $deliver = (new OrderDeliver())::detail(['order_id' => $this['order_id'], 'status' => 10]);
            if ($deliver) {
                $deliver->updateDeliver();
            }
            // 更新订单状态：已发货、已收货
            $status = $this->save([
                'delivery_status' => 20,
                'delivery_time' => time(),
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30
            ]);
            // 执行订单完成后的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$this], static::$app_id);
            return $status;
        });
    }


    /**
     * 获取已付款订单总数 (可指定某天)
     */
    public function getOrderData($startDate = null, $endDate = null, $type, $shop_supplier_id = 0, $order_type = -1)
    {
        $model = $this;

        !is_null($startDate) && $model = $model->where('pay_time', '>=', strtotime($startDate));

        if (is_null($endDate)) {
            !is_null($startDate) && $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }

        if ($shop_supplier_id > 0) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        if ($order_type >= 0) {
            $model = $model->where('order_type', '=', $order_type);
        }

        $model = $model->where('is_delete', '=', 0)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20);
        if ($type == 'order_total') {
            // 订单数量
            return $model->count();
        } else if ($type == 'order_total_price') {
            // 订单总金额
            return $model->sum('pay_price');
        } else if ($type == 'order_user_total') {
            // 支付用户数
            return count($model->distinct(true)->column('user_id'));
        } else if ($type == 'order_refund_money') {
            // 退款金额
            return $model->sum('refund_money');
        } else if ($type == 'order_refund_total') {
            // 退款订单数
            return $model->where('refund_money', '>', 0)->count();
        } else if ($type == 'income_price') {
            // 预计收入
            return $model->sum('pay_price') - $model->sum('refund_money');
        }
        return 0;
    }

    /**
     * 交易记录列表
     */
    public function getRecordList($data, $type = 0)
    {
        $model = $this;
        //门店
        if (isset($data['shop_supplier_id']) && $data['shop_supplier_id']) {
            $model = $model->where('shop_supplier_id', '=', $data['shop_supplier_id']);
        }
        //订单状态
        if (isset($data['order_status']) && $data['order_status']) {
            switch ($data['order_status']) {
                case '1'://待支付
                    $model = $model->where('pay_status', '=', 10)->where('order_status', '<>', 20);
                    break;
                case '2'://进行中
                    $model = $model->where('pay_status', '=', 20)->where('order_status', '=', 10);
                    break;
                case '3'://已取消
                    $model = $model->where('order_status', '=', 20);
                    break;
                case '4'://已完成
                    $model = $model->where('pay_status', '=', 20)->where('order_status', '=', 30);
                    break;
            }
        }
        //订单类型
        if (isset($data['order_type']) && $data['order_type'] >= 0) {
            $model = $model->where('order_type', '=', $data['order_type']);
        }
        //支付方式
        if (isset($data['pay_type']) && $data['pay_type']) {
            $model = $model->where('pay_type', '=', $data['pay_type']);
        }
        //查询日期
        switch ($data['type']) {
            case '1'://今天
                $model = $model->where('create_time', '>=', strtotime(date('Y-m-d')));
                break;
            case '2'://近7天
                $model = $model->where('create_time', '>=', strtotime(-6 . ' days', strtotime(date('Y-m-d'))));
                break;
            case '3'://近15天
                $model = $model->where('create_time', '>=', strtotime(-14 . ' days', strtotime(date('Y-m-d'))));
                break;
            case '4'://自定义
                $start = strtotime($data['time'][0]);
                $end = strtotime($data['time'][1]) + 86399;
                $model = $model->where('create_time', 'between', "$start,$end");
                break;
            default:
                $model = $model->where('create_time', '>=', strtotime(date('Y-m-d')));
                break;
        }
        // 获取数据列表
        if ($type == 0) {
            return $model->order(['create_time' => 'desc'])
                ->paginate($data);
        } else {
            return $model->order(['create_time' => 'desc'])
                ->select();
        }

    }

    /**
     * 获取各类型总销售额
     */
    public function getOrderTotalMoney($order_type, $shop_supplier_id, $data = [])
    {
        $model = $this;
        if (isset($data['type']) && $data['type']) {
            switch ($data['type']) {
                case '1'://今天
                    $model = $model->where('create_time', '=', strtotime(date('Y-m-d')));
                    break;
                case '2'://近7天
                    $model = $model->where('create_time', '>=', strtotime(-6 . ' days', strtotime(date('Y-m-d'))));
                    break;
                case '3'://近15天
                    $model = $model->where('create_time', '>=', strtotime(-14 . ' days', strtotime(date('Y-m-d'))));
                    break;
                case '4'://自定义
                    $start = strtotime($data['time'][0]);
                    $end = strtotime($data['time'][1]) + 86399;
                    $model = $model->where('create_time', 'between', "$start,$end");
                    break;
            }
        }
        if ($shop_supplier_id) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        $model = $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('order_type', '=', $order_type)
            ->where('is_delete', '=', 0);
        $detail['express_price'] = $model->sum('express_price') ? $model->sum('express_price') : 0;
        $detail['bag_price'] = $model->sum('bag_price') ? $model->sum('bag_price') : 0;
        $detail['product_price'] = $model->sum('total_price') ? $model->sum('total_price') : 0;
        $detail['refund_money'] = $model->sum('refund_money') ? $model->sum('refund_money') : 0;
        $detail['total_price'] = $model->sum('pay_price') ? $model->sum('pay_price') : 0;
        $detail['income_money'] = $detail['total_price'] - $detail['refund_money'];
        $detail['order_count'] = $model->count();
        return $detail;
    }

    /**
     * 获取商品销量Top10
     */
    public function getProductRank($type = 0, $product_type = -1, $shop_supplier_id = 0)
    {
        $model = new OrderProduct;
        if ($type == 0) {
            $order = 'total_num desc';
        } else {
            $order = 'total_price desc';
        }
        if ($product_type >= 0) {
            $model = $model->where('p.product_type', '=', $product_type);
        }
        if ($shop_supplier_id) {
            $model = $model->where('p.shop_supplier_id', '=', $shop_supplier_id);
        }
        $list = $model->alias('op')
            ->where('o.pay_status', '=', 20)
            ->where('o.order_status', '<>', 20)
            ->join('order o', 'op.order_id=o.order_id')
            ->join('product p', 'p.product_id=op.product_id')
            ->field('p.product_name,sum(total_pay_price) as total_price,sum(total_num) as total_num')
            ->group('op.product_id')
            ->order($order)
            ->limit(10)
            ->select();
        return $list;
    }
}