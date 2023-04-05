<?php


namespace app\common\model\plus\birthgift;

use app\common\model\BaseModel;

/**
 * 生日有礼模型
 */
class BirthGift extends BaseModel
{
    protected $pk = 'gift_id';
    protected $name = 'birth_gift';

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
     * 详情
     */
    public static function detail()
    {
        return static::find();
    }


}