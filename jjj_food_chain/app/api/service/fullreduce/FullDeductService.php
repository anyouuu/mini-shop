<?php

namespace app\api\service\fullreduce;

use app\common\library\helper;

/**
 * 满减抵扣金额
 */
class FullDeductService
{
    private $actualReducedMoney;

    public function setProductFullreduceMoney($productList, $reducedMoney)
    {
        // 统计订单商品总金额,(单位分)
        $orderTotalPrice = 0;
        foreach ($productList as &$product) {
            $product['total_price'] *= 100;
            $orderTotalPrice += $product['total_price'];
        }
        // 计算实际抵扣金额
        $this->setActualReducedMoney($reducedMoney, $orderTotalPrice);
        // 实际抵扣金额为0，
        if ($this->actualReducedMoney > 0) {
            // 计算商品的价格权重
            $productList = $this->getProductListWeight($productList, $orderTotalPrice);
            // 计算商品抵扣金额
            $this->setProductListMoney($productList);
            // 总抵扣金额
            $totalReduceMoney = helper::getArrayColumnSum($productList, 'fullreduce_money');
            $this->setProductListMoneyFill($productList, $totalReduceMoney);
            $this->setProductListMoneyDiff($productList, $totalReduceMoney);
        }
        return $productList;
    }

    public function getActualReducedMoney()
    {
        return $this->actualReducedMoney;
    }

    private function setActualReducedMoney($reducedMoney, $orderTotalPrice)
    {
        $reducedMoney *= 100;
        $this->actualReducedMoney = ($reducedMoney >= $orderTotalPrice) ? $orderTotalPrice - 1 : $reducedMoney;
    }

    private function arraySortByWeight($productList)
    {
        return array_sort($productList, 'weight', true);
    }

    private function getProductListWeight($productList, $orderTotalPrice)
    {
        foreach ($productList as &$product) {
            $product['weight'] = $product['total_price'] / $orderTotalPrice;
        }
        return $this->arraySortByWeight($productList);
    }

    private function setProductListMoney(&$productList)
    {
        foreach ($productList as &$product) {
            $product['fullreduce_money'] = bcmul($this->actualReducedMoney, $product['weight']);
        }
        return true;
    }

    private function setProductListMoneyFill(&$productList, $totalReduceMoney)
    {
        if ($totalReduceMoney === 0) {
            $temReducedMoney = $this->actualReducedMoney;
            foreach ($productList as &$product) {
                if ($temReducedMoney === 0) break;
                $product['fullreduce_money'] = 1;
                $temReducedMoney--;
            }
        }
        return true;
    }

    private function setProductListMoneyDiff(&$productList, $totalReduceMoney)
    {
        $tempDiff = $this->actualReducedMoney - $totalReduceMoney;
        foreach ($productList as &$product) {
            if ($tempDiff < 1) break;
            $product['fullreduce_money']++ && $tempDiff--;
        }
        return true;
    }

}