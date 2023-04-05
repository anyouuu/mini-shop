<?php


namespace app\common\model\plus\queue;

use app\common\model\BaseModel;

/**
 * 排队取号记录模型
 */
class QueueRecord extends BaseModel
{
    protected $pk = 'record_id';
    protected $name = 'queue_record';
    /**
     * 追加字段
     * @var string[]
     */
    protected $append = [
        'state_text',
    ];

    /**
     * 订单状态文字描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getStateTextAttr($value, $data)
    {
        $status = [10 => '待叫号', 20 => '已入座', 30 => '已过号', 40 => '已取消'];
        return $status[$data['status']];
    }

    /**
     * 关联门店
     */
    public function supplier()
    {
        return $this->BelongsTo('app\\common\\model\\supplier\\Supplier', 'shop_supplier_id', 'shop_supplier_id');
    }

    /**
     * 关联门店
     */
    public function tables()
    {
        return $this->BelongsTo('app\\common\\model\\plus\\queue\\QueueTable', 'table_id', 'table_id');
    }

    /**
     * 桌位详情
     */
    public static function detail($where)
    {
        $filter = is_array($where) ? $where : ['record_id' => $where];
        return static::where($filter)->find();
    }


}