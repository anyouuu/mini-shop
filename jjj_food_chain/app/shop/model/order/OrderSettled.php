<?php

namespace app\shop\model\order;

use app\common\model\order\OrderSettled as OrderSettledModel;
use app\common\model\supplier\Cash as CashModel;

/**
 * 订单结算模型
 */
class OrderSettled extends OrderSettledModel
{
    /**
     * 获取数据概况
     */
    public function getSettledData($shop_supplier_id)
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $data = [
            // 供应商总金额
            'supplier_money' => [
                'total' => number_format($this->getDatas(null, null, 'supplier_money', $shop_supplier_id)),
                'today' => number_format($this->getDatas($today, null, 'supplier_money', $shop_supplier_id)),
                'yesterday' => number_format($this->getDatas($yesterday, $yesterday, 'supplier_money', $shop_supplier_id))
            ],
            // 供应商待结算
            'settle_money' => [
                'total' => number_format($this->getDatas(null, null, 'settle_money', $shop_supplier_id)),
                'today' => number_format($this->getDatas($today, null, 'settle_money', $shop_supplier_id)),
                'yesterday' => number_format($this->getDatas($yesterday, $yesterday, 'settle_money', $shop_supplier_id))
            ],
            // 供应商已结算
            'real_supplier_money' => [
                'total' => number_format($this->getDatas(null, null, 'real_supplier_money', $shop_supplier_id)),
                'today' => number_format($this->getDatas($today, null, 'real_supplier_money', $shop_supplier_id)),
                'yesterday' => number_format($this->getDatas($yesterday, $yesterday, 'real_supplier_money', $shop_supplier_id))
            ],
            // 退款金额
            'refund_money' => [
                'total' => number_format($this->getDatas(null, null, 'refund_money', $shop_supplier_id)),
                'today' => number_format($this->getDatas($today, null, 'refund_money', $shop_supplier_id)),
                'yesterday' => number_format($this->getDatas($yesterday, $yesterday, 'refund_money', $shop_supplier_id))
            ],
            // 提现金额
            'cash_money' => [
                'total' => number_format($this->getDatas(null, null, 'cash_money', $shop_supplier_id)),
                'today' => number_format($this->getDatas($today, null, 'cash_money', $shop_supplier_id)),
                'yesterday' => number_format($this->getDatas($yesterday, $yesterday, 'cash_money', $shop_supplier_id))
            ],
            // 提现金额
            'income' => [
                'total' => number_format($this->getDatas(null, null, 'income', $shop_supplier_id)),
                'today' => number_format($this->getDatas($today, null, 'income', $shop_supplier_id)),
                'yesterday' => number_format($this->getDatas($yesterday, $yesterday, 'income', $shop_supplier_id))
            ],
        ];
        return $data;
    }

    /**
     * 按日期获取结算数据
     */
    public function getSettledDataByDate($days, $shop_supplier_id)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'real_supplier_money' => $this->getDatas($day, $day, 'real_supplier_money', $shop_supplier_id),
                'refund_money' => $this->getDatas($day, $day, 'refund_money', $shop_supplier_id),
                'supplier_money' => $this->getDatas($day, $day, 'supplier_money', $shop_supplier_id),
                'settle_money' => $this->getDatas($day, $day, 'settle_money', $shop_supplier_id),
                'cash_money' => $this->getDatas($day, $day, 'cash_money', $shop_supplier_id),
                'order_count' => $this->getDatas($day, $day, 'order_count', $shop_supplier_id),
            ];
        }
        return $data;
    }

    /**
     * 获取供应商统计数量
     */
    public function getDatas($startDate = null, $endDate = null, $type = 'real_supplier_money', $shop_supplier_id)
    {
        if ($type == "cash_money") {
            $model = new CashModel;
        } else {
            $model = new Order;
            $model = $model->where('pay_status', '=', 20);
        }
        if ($shop_supplier_id) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        if ($startDate && $endDate) {
            $model = $model->where('create_time', 'between', [strtotime($startDate), strtotime($endDate) + 86399]);
        } else if ($startDate) {
            $model = $model->where('create_time', '>', strtotime($startDate));
        }
        if ($type == 'real_supplier_money') {
            return $model->where('order_status', '=', 30)->sum('pay_price');
        } else if ($type == 'refund_money') {
            return $model->where('order_status', 'in', '10,30')->sum('refund_money');
        } else if ($type == 'supplier_money') {
            return $model->where('order_status', 'in', '10,30')->sum('pay_price');
        } else if ($type == 'settle_money') {
            return $model->where('order_status', '=', 10)->sum('pay_price');
        } else if ($type == 'cash_money') {
            return $model->where('apply_status', '=', 40)->sum('money');
        } else if ($type == 'order_count') {
            return $model->where('order_status', 'in', '10,30')->count();
        } else if ($type == 'income') {
            $income = $model->where('order_status', 'in', '10,30')->field("sum(pay_price-refund_money) as income")->find();
            return $income['income'];
        }
        return 0;
    }


    /**
     * 获取售后单列表
     */
    public function getList($params)
    {
        $model = $this;
        // 查询条件：订单号
        if (isset($params['order_no']) && !empty($params['order_no'])) {
            $model = $model->where('order.order_no', 'like', "%{$params['order_no']}%");
        }
        if (isset($params['start_day']) && !empty($params['start_day'])) {
            $model = $model->where('settled.create_time', '>=', strtotime($params['start_day']));
        }
        if (isset($params['end_day']) && !empty($params['end_day'])) {
            $model = $model->where('settled.create_time', '<', strtotime($params['end_day']));
        }
        // 是否结算
        if (isset($params['is_settled']) && $params['is_settled'] > -1) {
            $model = $model->where('settled.is_settled', '=', $params['is_settled']);
        }
        if (isset($params['shop_supplier_id']) && $params['shop_supplier_id']) {
            $model = $model->where('settled.shop_supplier_id', '=', $params['shop_supplier_id']);
        }
        // 获取列表数据
        return $model->alias('settled')->field('settled.*')
            ->with(['orderMaster', 'supplier'])
            ->join('order', 'order.order_id = settled.order_id')
            ->order(['settled.create_time' => 'desc'])
            ->paginate($params, false, [
                'query' => \request()->request()
            ]);
    }
}