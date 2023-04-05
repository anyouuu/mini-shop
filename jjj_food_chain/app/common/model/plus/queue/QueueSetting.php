<?php


namespace app\common\model\plus\queue;

use app\common\model\BaseModel;

/**
 * 排号设置模型
 */
class QueueSetting extends BaseModel
{
    protected $pk = 'setting_id';
    protected $name = 'queue_setting';

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
    public static function detail($shop_supplier_id)
    {
        return static::where('shop_supplier_id', '=', $shop_supplier_id)->find();
    }


}