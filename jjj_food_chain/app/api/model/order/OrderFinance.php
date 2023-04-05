<?php

namespace app\api\model\order;

use app\common\model\order\OrderFinance as OrderFinanceModel;

/**
 * 财务对账
 */
class OrderFinance extends OrderFinanceModel
{
    //添加对账
    public function add($data)
    {
        $time = strtotime(date('Y-m-d'));
        $finance = $this->where('shop_supplier_id', '=', $data['shop_supplier_id'])
            ->where('time', '=', $time)
            ->find();
        $finance_id = 0;
        if ($finance) {
            $update['order_count'] = $finance['order_count'] + 1;
            $update['total_money'] = $finance['total_money'] + $data['pay_price'];
            $update['revenue_money'] = $finance['revenue_money'] + $data['pay_price'] - $data['refund_money'];
            $update['refund_money'] = $finance['refund_money'] + $data['refund_money'];
            if ($data['order_type'] == 0) {
                $update['out_order_count'] = $finance['out_order_count'] + 1;
                $update['out_total_money'] = $finance['out_total_money'] + $data['pay_price'];
                $update['out_revenue_money'] = $finance['out_revenue_money'] + $data['pay_price'] - $data['refund_money'];
                $update['out_refund_money'] = $finance['out_refund_money'] + $data['refund_money'];
            }else{
                $update['in_order_count'] = $finance['in_order_count'] + 1;
                $update['in_total_money'] = $finance['in_total_money'] + $data['pay_price'];
                $update['in_revenue_money'] = $finance['in_revenue_money'] + $data['pay_price'] - $data['refund_money'];
                $update['in_refund_money'] = $finance['in_refund_money'] + $data['refund_money'];
            }
            $finance->save($update);
            $finance_id = $finance['finance_id'];
        } else {
            $add['time'] = $time;
            $add['total_money'] = $data['pay_price'];
            $add['revenue_money'] = $data['pay_price'] - $data['refund_money'];
            $add['refund_money'] = $data['refund_money'];
            $add['order_count'] = 1;
            $add['shop_supplier_id'] = $data['shop_supplier_id'];
            $add['app_id'] = $data['app_id'];
            if ($data['order_type'] == 0) {
                $add['out_order_count'] = 1;
                $add['out_total_money'] = $data['pay_price'];
                $add['out_revenue_money'] = $data['pay_price'] - $data['refund_money'];
                $add['out_refund_money'] = $data['refund_money'];
            }else{
                $add['in_order_count'] = 1;
                $add['in_total_money'] = $data['pay_price'];
                $add['in_revenue_money'] = $data['pay_price'] - $data['refund_money'];
                $add['in_refund_money'] = $data['refund_money'];
            }
            $this->save($add);
            $finance_id = $this['finance_id'];
        }
        (new Order)->where('order_id', '=', $data['order_id'])->save(['finance_id' => $finance_id]);
    }
}