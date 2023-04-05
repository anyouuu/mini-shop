<?php

namespace app\admin\model\supplier;

use app\common\model\supplier\Supplier as SupplierModel;

/**
 * 门店模型
 */
class Supplier extends SupplierModel
{
    /**
     * 添加
     */
    public function add($data, $app_id)
    {
        //添加门店
        $data['password'] = salt_hash($data['password']);
        $data['real_name'] = $data['user_name'];
        $data['name'] = $data['user_name'];
        $data['app_id'] = $app_id;
        $data['is_main'] = 1;
        $this->save($data);
    }
}