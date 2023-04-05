<?php

namespace app\shop\controller\statistics;

use app\shop\controller\Controller;
use app\common\service\statistics\OrderService;

/**
 * 销售数据控制器
 */
class Sales extends Controller
{

    /**
     * 通过时间段查询本期上期金额
     * $type类型：order refund
     */
    public function order($search_time, $type = 'order')
    {
        $days = $this->getDays($search_time);
        $data = [];
        $user = $this->store['user'];
        $shop_supplier_id = 0;
        if ($user['user_type'] == 1) {
            $shop_supplier_id = $user['shop_supplier_id'];
        }
        if ($type == 'order') {
            $data = (new OrderService($shop_supplier_id))->getDataByDate($days);
        } else if ($type == 'refund') {
            $data = (new OrderService($shop_supplier_id))->getRefundByDate($days);
        }
        return $this->renderSuccess('', [
            // 日期
            'days' => $days,
            // 数据
            'data' => $data,
        ]);
    }


    /**
     * 获取具体日期数组
     */
    private function getDays($search_time)
    {
        //搜索时间段
        if (!isset($search_time) || empty($search_time)) {
            //没有传，则默认为最近7天
            $end_time = date('Y-m-d', time());
            $start_time = date('Y-m-d', strtotime('-7 day', time()));
        } else {
            $start_time = array_shift($search_time);
            $end_time = array_pop($search_time);
        }

        $dt_start = strtotime($start_time);
        $dt_end = strtotime($end_time);
        $date = [];
        $date[] = date('Y-m-d', strtotime($start_time));
        while ($dt_start < $dt_end) {
            $date[] = date('Y-m-d', strtotime('+1 day', $dt_start));
            $dt_start = strtotime('+1 day', $dt_start);
        }
        return $date;
    }
}