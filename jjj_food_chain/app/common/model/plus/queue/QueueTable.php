<?php


namespace app\common\model\plus\queue;

use app\common\model\BaseModel;

/**
 * 桌位模型
 */
class QueueTable extends BaseModel
{
    protected $pk = 'table_id';
    protected $name = 'queue_table';

    /**
     * 关联门店
     */
    public function supplier()
    {
        return $this->BelongsTo('app\\common\\model\\supplier\\Supplier', 'shop_supplier_id', 'shop_supplier_id');
    }

    /**
     * 桌位详情
     */
    public static function detail($where)
    {
        $filter = is_array($where) ? $where : ['table_id' => $where];
        return static::where($filter)->find();
    }


}