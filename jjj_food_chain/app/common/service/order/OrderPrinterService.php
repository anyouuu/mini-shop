<?php

namespace app\common\service\order;

use app\common\model\settings\Setting as SettingModel;
use app\common\model\settings\Printer as PrinterModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\printer\Driver as PrinterDriver;

/**
 * 订单打印服务类
 */
class OrderPrinterService
{
    /**
     * 执行订单打印
     */
    public function printTicket($order)
    {
        // 打印机设置
        $printerConfig = SettingModel::getSupplierItem('printer', $order['shop_supplier_id'], $order['app_id']);
        // 商家打印
        $this->sellerPrint($printerConfig, $order);
        // 商家打印
        $this->buyerPrint($printerConfig, $order);
        // 商家打印
        $this->roomPrint($printerConfig, $order);
    }

    /**
     * 商家打印
     */
    public function sellerPrint($printerConfig, $order)
    {
        // 判断是否开启商家打印设置
        if (!$printerConfig['seller_open'] || !$printerConfig['seller_printer_id']) {
            return false;
        }
        // 获取当前的打印机
        $printer = PrinterModel::detail($printerConfig['seller_printer_id']);
        if (empty($printer) || $printer['is_delete']) {
            return false;
        }
        // 实例化打印机驱动
        $PrinterDriver = new PrinterDriver($printer);
        // 获取订单打印内容
        $content = $this->getPrintContent($order);
        // 执行打印请求
        return $PrinterDriver->printTicket($content);
    }

    /**
     * 顾客打印
     */
    public function buyerPrint($printerConfig, $order)
    {
        // 判断是否开启商家打印设置
        if (!$printerConfig['buyer_open'] || !$printerConfig['buyer_printer_id']) {
            return false;
        }
        // 获取当前的打印机
        $printer = PrinterModel::detail($printerConfig['buyer_printer_id']);
        if (empty($printer) || $printer['is_delete']) {
            return false;
        }
        // 实例化打印机驱动
        $PrinterDriver = new PrinterDriver($printer);
        // 获取订单打印内容
        $content = $this->getPrintContent($order);
        // 执行打印请求
        return $PrinterDriver->printTicket($content);
    }

    /**
     * 厨房打印
     */
    public function roomPrint($printerConfig, $order)
    {
        // 判断是否开启商家打印设置
        if (!$printerConfig['room_open'] || !$printerConfig['room_printer_id']) {
            return false;
        }
        // 获取当前的打印机
        $printer = PrinterModel::detail($printerConfig['room_printer_id']);
        if (empty($printer) || $printer['is_delete']) {
            return false;
        }
        // 实例化打印机驱动
        $PrinterDriver = new PrinterDriver($printer);
        // 获取订单打印内容
        $content = $this->getPrintContent($order);
        // 执行打印请求
        return $PrinterDriver->printTicket($content);
    }

    /**
     * 构建订单打印的内容
     */
    private function getPrintContent($order)
    {
        // 商城名称
        $storeName = SettingModel::getItem('store', $order['app_id'])['name'];
        // 收货地址
        $address = $order['address'];
        // 拼接模板内容
        $content = "<C>*店铺名称({$order['supplier']['name']})*</C><BR>";
        $content .= "<CB>{$order['pay_type']['text']}</CB><BR>";
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXPRESS || $order['delivery_type']['value'] == DeliveryTypeEnum::EXTRACT) {
            $content .= "<CB>立即送达</CB><BR>";
            $content .= $order['delivery_type']['value'] == 10 ? "预计送达时间：" . $order['mealtime'] . "<BR>" : "预计送达时间：" . "立即自取" . "<BR>";
        }
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::TAKEAWAY) {
            $content .= "<CB>打包带走</CB><BR>";
        }
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::DINNER) {
            $content .= "<CB>店内就餐</CB><BR>";
        }
        $content .= '--------------------------------<BR>';
        $content .= "昵称：{$order['user']['nickName']}<BR>";
        $content .= "订单编号：{$order['order_no']}<BR>";
        $content .= '下单时间：' . date('Y-m-d H:i:s', $order['pay_time']) . '<BR>';
        // 买家备注
        if (!empty($order['buyer_remark'])) {
            $content .= "<B>备注:{$order['buyer_remark']}</B><BR>";
            $content .= '--------------------------------<BR>';
        } else {
            $content .= '--------------------------------<BR>';
        }
        $content .= '名称               数量    金额<BR>';
        $content .= '--------------------------------<BR>';
        foreach ($order['product'] as $key => $product) {
            if ($product['product_attr']) {
                $content .= $product['product_name'] . '(' . $product['product_attr'] . ')' . '<BR>';
            } else {
                $content .= $product['product_name'] . '<BR>';
            }
            $content .= '<RIGHT>' . $product['total_num'] . '     ' . $product['total_price'] . '</RIGHT>' . '<BR>';
        }
        $content .= '------------其它费用------------<BR>';
        // 配送费
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXPRESS) {
            $content .= "[配送费：+{$order['express_price']}]<BR>";
        }
        //包装费
        if ($order['bag_price'] > 0) {
            $content .= "[包装费：+{$order['bag_price']}]<BR>";
        }
        // 优惠券
        if ($order['coupon_money'] > 0) {
            $content .= "[优惠券优惠：-{$order['coupon_money']}]<BR>";
        }
        // 订单金额
        if ($order['fullreduce_money'] > 0) {
            $content .= "[满减优惠：-{$order['fullreduce_money']}]<BR>";
        }
        $content .= "<BR>";
        // 实付款
        //$content .= "<RIGHT>实付款：<BOLD><B>{$order['pay_price']}</B></BOLD>元</RIGHT><BR>";
        $content .= "<BOLD><B>实付：￥{$order['pay_price']}</B></BOLD><BR>";
        // 收货人信息
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXPRESS) {
            $content .= "--------------------------------<BR>";
            $content .= "收货人：{$address['name']}<BR>";
            $content .= "联系电话：{$address['phone']}<BR>";
            $content .= '收货地址：' . $address->getFullAddress() . '<BR>';
        }
        // 自提信息
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXTRACT && !empty($order['extract'])) {
            $content .= "--------------------------------<BR>";
            $content .= "联系电话：{$order['extract']['phone']}<BR>";
        }
        $content .= "--------------------------------<BR>";
        $content .= "<CB>----#1完----</CB><BR>";
        return $content;
    }

    /**
     * 执行取号打印
     */
    public function printQueueTicket($data, $printer_id)
    {
        // 获取当前的打印机
        $printer = PrinterModel::detail($printer_id);
        if (empty($printer) || $printer['is_delete']) {
            return false;
        }
        // 实例化打印机驱动
        $PrinterDriver = new PrinterDriver($printer);
        // 获取订单打印内容
        $content = $this->getQueuePrintContent($data);
        // 执行打印请求
        return $PrinterDriver->printTicket($content);
    }

    /**
     * 构建订单打印的内容
     */
    private function getQueuePrintContent($data)
    {
        $time = date('Y-m-d H:i:s');
        // 拼接模板内容
        $content = "<C>-------{$data['name']}-------</C><BR>";
        $content .= "<BR>";
        $content .= "<CB>{$data['queue_no']}</CB><BR>";
        $content .= "<BR>";
        $content .= "<C>排队中{$data['wait_num']}人</C><BR>";
        $content .= "<C>一次有效  过号作废</C><BR>";
        $content .= "<C>{$time}</C><BR>";
        $content .= '--------------------------------<BR>';
        return $content;
    }

}