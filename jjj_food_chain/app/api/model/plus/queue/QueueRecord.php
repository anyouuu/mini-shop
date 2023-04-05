<?php


namespace app\api\model\plus\queue;

use app\common\model\plus\queue\QueueRecord as QueueRecordModel;
use app\common\service\order\OrderPrinterService;
use app\api\model\supplier\Supplier as SupplierModel;

/**
 * 排队取号记录模型
 */
class QueueRecord extends QueueRecordModel
{
    //添加取号记录
    public function take($param, $user)
    {
        try {
            $param['user_id'] = $user['user_id'];
            $param['app_id'] = self::$app_id;
            //查询今日订单号
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
            $num = $this->where('create_time', 'between', "$beginToday,$endToday")->count();
            if ($num > 0) {
                $queue_no = 'A' . $this->createNo($num + 1);
            } else {
                $queue_no = "A0001";
            }
            $param['queue_no'] = $queue_no;
            // 号码打印
            $setting = (new QueueSetting())::detail($param['shop_supplier_id']);
            if ($setting && $setting['is_print'] && $setting['printer_id']) {
                $supplier = SupplierModel::detail($param['shop_supplier_id']);
                $param['name'] = $supplier['name'];
                $param['wait_num'] = (new QueueRecord())
                    ->where('shop_supplier_id', '=', $param['shop_supplier_id'])
                    ->where('status', '=', 10)
                    ->count();
                (new OrderPrinterService)->printQueueTicket($param, $setting['printer_id']);
            }
            $this->save($param);
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取列表数据
     */
    public function getList($params, $user)
    {
        $model = $this;
        // 查询列表数据
        return $model->where('shop_supplier_id', '=', $params['shop_supplier_id'])
            ->where('user_id', '=', $user['user_id'])
            ->order(['create_time' => 'desc'])
            ->paginate($params);
    }

    public function createNo($num)
    {
        if ($num < 10) {
            $num = '000' . $num;
        }
        if ($num >= 10 && $num < 100) {
            $num = '00' . $num;
        }
        if ($num >= 100 && $num < 1000) {
            $num = '0' . $num;
        }
        if ($num >= 1000 && $num < 10000) {
            $num = '0' . $num;
        }
        if ($num >= 10000 && $num < 100000) {
            $num = '0' . $num;
        }
        if ($num >= 100000 && $num < 1000000) {
            $num = '0' . $num;
        }
        return $num;
    }
}