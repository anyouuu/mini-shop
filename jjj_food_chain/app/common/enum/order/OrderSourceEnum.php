<?php

namespace app\common\enum\order;

use MyCLabs\Enum\Enum;

/**
 * 订单来源
 */
class OrderSourceEnum extends Enum
{
    // 普通订单
    const MASTER = 10;
    // 积分订单
    const POINTS = 20;
    // 拼团
    const ASSEMBLE = 30;
    // 砍价
    const BARGAIN = 40;
    // 秒杀
    const SECKILL = 50;
    //礼包购
    const GIFT = 60;

    /**
     * 获取枚举数据
     */
    public static function data()
    {
        return [
            self::MASTER => [
                'name' => '普通',
                'value' => self::MASTER,
            ],
            self::POINTS => [
                'name' => '积分',
                'value' => self::POINTS,
            ],
            self::ASSEMBLE => [
                'name' => '拼团',
                'value' => self::ASSEMBLE,
            ],
            self::BARGAIN => [
                'name' => '砍价',
                'value' => self::BARGAIN,
            ],
            self::SECKILL => [
                'name' => '秒杀',
                'value' => self::SECKILL,
            ],
            self::GIFT => [
                'name' => '礼包购',
                'value' => self::GIFT,
            ],

        ];
    }

}