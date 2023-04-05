<?php

namespace app\common\model\supplier;

use app\common\model\BaseModel;

/**
 * 供应商资金明细模型
 */
class Capital extends BaseModel
{
    protected $name = 'supplier_capital';
    protected $pk = 'id';

    /**
     * 关联供应商表
     */
    public function supplier()
    {
        return $this->belongsTo('app\\common\\model\\supplier\\Supplier', 'shop_supplier_id', 'shop_supplier_id');
    }

    /**
     * 分销商资金明细
     * @param $data
     */
    public static function add($data)
    {
        $model = new static;
        $model->save(array_merge([
            'app_id' => $model::$app_id
        ], $data));
    }
}