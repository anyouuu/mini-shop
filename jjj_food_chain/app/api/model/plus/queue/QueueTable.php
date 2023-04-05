<?php


namespace app\api\model\plus\queue;

use app\common\model\plus\queue\QueueTable as QueueTableModel;

/**
 * 桌位模型
 */
class QueueTable extends QueueTableModel
{
    //获取全部桌位
    public function getAllList($shop_supplier_id)
    {
        $model = $this;
        $list = $model->where('shop_supplier_id', '=', $shop_supplier_id)
            ->where('status', '=', 1)
            ->order('sort asc,max_num asc')
            ->select();
        foreach ($list as &$item) {
            $item['wait_num'] = (new QueueRecord())
                ->where('table_id', '=', $item['table_id'])
                ->where('status', '=', 10)
                ->count();
            $item['wait_times'] = $item['wait_num'] * $item['wait_time'];
        }
        return $list;
    }

    //获取全部桌位人数
    public function getAllNum($shop_supplier_id)
    {
        $model = $this;
        $max_num = $model->where('shop_supplier_id', '=', $shop_supplier_id)
            ->where('status', '=', 1)
            ->order('max_num desc')
            ->value('max_num');
        if (!$max_num) {
            return [];
        }
        $numArr = [];
        for ($i = 1; $i <= $max_num; $i++) {
            $numArr[$i - 1]['num'] = $i;
            $table = $this->where('max_num', '>=', $i)
                ->where('shop_supplier_id', '=', $shop_supplier_id)
                ->where('status', '=', 1)
                ->order('max_num asc')
                ->find();
            $table['wait_num'] = (new QueueRecord())
                ->where('table_id', '=', $table['table_id'])
                ->where('status', '=', 10)
                ->count();
            $table['wait_times'] = $table['wait_num'] * $table['wait_time'];
            $numArr[$i - 1]['table'] = $table;
        }
        return $numArr;
    }
}