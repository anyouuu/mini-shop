<?php

namespace app\common\service\product\factory;

use app\common\enum\order\OrderSourceEnum;

/**
 * 商品辅助工厂类
 */
class ProductFactory
{
    public static function getFactory($type = OrderSourceEnum::MASTER)
    {
        switch ($type) {
            case OrderSourceEnum::MASTER:
                return new MasterProductService();
                break;
            case OrderSourceEnum::POINTS;
                return new PointsProductService();
                break;
            case OrderSourceEnum::SECKILL:
                return new SeckillProductService();
                break;
            case OrderSourceEnum::BARGAIN:
                return new BargainProductService();
                break;
            case OrderSourceEnum::ASSEMBLE:
                return new AssembleProductService();
                break;
        }
    }
}