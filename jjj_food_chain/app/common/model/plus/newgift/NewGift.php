<?php


namespace app\common\model\plus\newgift;

use app\common\model\BaseModel;

/**
 * 新人礼包模型
 */
class NewGift extends BaseModel
{
    protected $pk = 'gift_id';
    protected $name = 'new_gift';

    /**
     * 优惠券数组转换
     * @param $value
     * @param $data
     * @return string
     */
    public function getCouponIdsAttr($value)
    {
        return $value ? explode(',', $value) : '';
    }

    /**
     * 关联门店
     */
    public function supplier()
    {
        return $this->BelongsTo('app\\common\\model\\supplier\\Supplier', 'shop_supplier_id', 'shop_supplier_id');
    }

    /**
     * 详情
     */
    public static function detail($shop_supplier_id)
    {
        return static::where('shop_supplier_id', '=', $shop_supplier_id)->find();
    }


}