<?php

namespace app\common\enum\settings;

use MyCLabs\Enum\Enum;
/**
 * 配送方式枚举类
 */
class DeliverySourceEnum extends Enum
{
    // 商家配送
    const SELLER = 10;
    // 达达配送
    const DADA = 20;
    /**
     * 获取枚举数据
     */
    public static function data()
    {
        return [
            self::SELLER => [
                'name' => '商家配送',
                'value' => self::SELLER,
            ],
            self::DADA => [
                'name' => '达达配送',
                'value' => self::DADA,
            ],
        ];
    }
}