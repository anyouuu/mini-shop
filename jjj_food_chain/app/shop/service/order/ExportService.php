<?php

namespace app\shop\service\order;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * 订单导出服务类
 */
class ExportService
{
    /**
     * 订单导出
     */
    public function orderList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列宽
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);

        //设置工作表标题名称
        $sheet->setTitle('订单明细');

        $sheet->setCellValue('A1', '订单号');
        $sheet->setCellValue('B1', '商品信息');
        $sheet->setCellValue('C1', '订单总额');
        $sheet->setCellValue('D1', '优惠券抵扣');
        $sheet->setCellValue('E1', '满减金额');
        $sheet->setCellValue('F1', '配送费');
        $sheet->setCellValue('G1', '包装费');
        $sheet->setCellValue('H1', '实付款金额');
        $sheet->setCellValue('I1', '支付方式');
        $sheet->setCellValue('J1', '下单时间');
        $sheet->setCellValue('K1', '买家');
        $sheet->setCellValue('L1', '买家留言');
        $sheet->setCellValue('M1', '配送方式');
        $sheet->setCellValue('N1', '自提联系电话');
        $sheet->setCellValue('O1', '收货人姓名');
        $sheet->setCellValue('P1', '联系电话');
        $sheet->setCellValue('Q1', '收货人地址');
        $sheet->setCellValue('R1', '付款状态');
        $sheet->setCellValue('S1', '付款时间');
        $sheet->setCellValue('T1', '核销时间');
        $sheet->setCellValue('U1', '订单状态');
        $sheet->setCellValue('V1', '微信支付交易号');

        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $address = $order['address'];
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order_no'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $this->filterProductInfo($order));
            $sheet->setCellValue('C' . ($index + 2), $order['order_price']);
            $sheet->setCellValue('D' . ($index + 2), $order['coupon_money']);
            $sheet->setCellValue('E' . ($index + 2), $order['fullreduce_money']);
            $sheet->setCellValue('F' . ($index + 2), $order['express_price']);
            $sheet->setCellValue('G' . ($index + 2), $order['bag_price']);
            $sheet->setCellValue('H' . ($index + 2), $order['pay_price']);
            $sheet->setCellValue('I' . ($index + 2), $order['pay_type']['text']);
            $sheet->setCellValue('J' . ($index + 2), $order['create_time']);
            $sheet->setCellValue('K' . ($index + 2), $order['user']['nickName']);
            $sheet->setCellValue('L' . ($index + 2), $order['buy_remark']);
            $sheet->setCellValue('M' . ($index + 2), $order['delivery_type']['text']);
            $sheet->setCellValue('N' . ($index + 2), !empty($order['extract']) ? "\t" . $order['extract']['phone'] . "\t" : '');
            $sheet->setCellValue('O' . ($index + 2), $order['address']['name']);
            $sheet->setCellValue('P' . ($index + 2), "\t" . $order['address']['phone'] . "\t");
            $sheet->setCellValue('Q' . ($index + 2), $address ? $address->getFullAddress() : '');
            $sheet->setCellValue('R' . ($index + 2), $order['pay_status']['text']);
            $sheet->setCellValue('S' . ($index + 2), $this->filterTime($order['pay_time']));
            $sheet->setCellValue('T' . ($index + 2), $this->filterTime($order['receipt_time']));
            $sheet->setCellValue('U' . ($index + 2), $order['order_status']['text']);
            $sheet->setCellValue('V' . ($index + 2), $order['transaction_id']);
            $index++;
        }

        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '订单') . '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 订单导出
     */
    public function deliverList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列宽
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(10);

        //设置工作表标题名称
        $sheet->setTitle('订单明细');

        $sheet->setCellValue('A1', '订单号');
        $sheet->setCellValue('B1', '订单金额');
        $sheet->setCellValue('C1', '订单状态');
        $sheet->setCellValue('D1', '配送方式');
        $sheet->setCellValue('E1', '配送费');
        $sheet->setCellValue('F1', '配送状态');
        $sheet->setCellValue('G1', '配送时间');
        $sheet->setCellValue('H1', '送达时间');
        $sheet->setCellValue('I1', '收货人姓名');
        $sheet->setCellValue('J1', '联系电话');
        $sheet->setCellValue('L1', '收货人地址');
        $sheet->setCellValue('L1', '配送员');
        $sheet->setCellValue('M1', '配送员电话');

        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $address = $order['address'];
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order_no'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['orders']['order_price']);
            $sheet->setCellValue('C' . ($index + 2), $order['orders']['order_status']['text']);
            $sheet->setCellValue('D' . ($index + 2), $order['deliver_source_text']);
            $sheet->setCellValue('E' . ($index + 2), $order['price']);
            $sheet->setCellValue('F' . ($index + 2), $order['deliver_status_text']);
            $sheet->setCellValue('G' . ($index + 2), $order['create_time']);
            $sheet->setCellValue('H' . ($index + 2), $this->filterTime($order['deliver_time']));
            $sheet->setCellValue('I' . ($index + 2), $order['orders']['address']['name']);
            $sheet->setCellValue('J' . ($index + 2), "\t" . $order['orders']['address']['phone'] . "\t");
            $sheet->setCellValue('K' . ($index + 2), $address ? $address->getFullAddress() : '');
            $sheet->setCellValue('L' . ($index + 2), $order['linkman']);
            $sheet->setCellValue('M' . ($index + 2), $order['phone']);
            $index++;
        }

        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '订单配送信息') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 格式化商品信息
     */
    private function filterProductInfo($order)
    {
        $content = '';
        foreach ($order['product'] as $key => $product) {
            $content .= ($key + 1) . ".商品名称：{$product['product_name']}\n";
            !empty($product['product_attr']) && $content .= "　商品规格：{$product['product_attr']}\n";
            $content .= "　购买数量：{$product['total_num']}\n";
            $content .= "　商品总价：{$product['total_price']}元\n\n";
        }
        return $content;
    }

    /**
     * 订单导出
     */
    public function financeOrderList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        //设置工作表标题名称
        $sheet->setTitle('订单明细');
        $sheet->setCellValue('A1', '订单号');
        $sheet->setCellValue('B1', '订单来源');
        $sheet->setCellValue('C1', '应收金额');
        $sheet->setCellValue('D1', '优惠金额');
        $sheet->setCellValue('E1', '实付金额');
        $sheet->setCellValue('F1', '预计收入');
        $sheet->setCellValue('G1', '订单状态');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order_no'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['order_type_text']);
            $sheet->setCellValue('C' . ($index + 2), $order['order_price']);
            $sheet->setCellValue('D' . ($index + 2), $order['order_price'] - $order['pay_price']);
            $sheet->setCellValue('E' . ($index + 2), $order['pay_price']);
            $sheet->setCellValue('F' . ($index + 2), $order['pay_price'] - $order['refund_money']);
            $sheet->setCellValue('G' . ($index + 2), $order['order_status']['text']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '订单') . '-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 订单导出
     */
    public function recordOrderList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        //设置工作表标题名称
        $sheet->setTitle('订单交易明细');
        $sheet->setCellValue('A1', '订单号');
        $sheet->setCellValue('B1', '订单类型');
        $sheet->setCellValue('C1', '总金额');
        $sheet->setCellValue('D1', '优惠金额');
        $sheet->setCellValue('E1', '实付金额');
        $sheet->setCellValue('F1', '实际到账');
        $sheet->setCellValue('G1', '支付方式');
        $sheet->setCellValue('H1', '订单状态');
        $sheet->setCellValue('I1', '下单时间');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order_no'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['order_type_text']);
            $sheet->setCellValue('C' . ($index + 2), $order['order_price']);
            $sheet->setCellValue('D' . ($index + 2), $order['order_price'] - $order['pay_price']);
            $sheet->setCellValue('E' . ($index + 2), $order['pay_price']);
            $sheet->setCellValue('F' . ($index + 2), $order['pay_price'] - $order['refund_money']);
            $sheet->setCellValue('G' . ($index + 2), $order['pay_type']['text']);
            $sheet->setCellValue('H' . ($index + 2), $order['order_status']['text']);
            $sheet->setCellValue('I' . ($index + 2), $order['create_time']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '订单') . '-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 订单导出
     */
    public function ProductRank($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        //设置工作表标题名称
        $sheet->setTitle('商品销量明细');
        $sheet->setCellValue('A1', '排名');
        $sheet->setCellValue('B1', '商品名称');
        $sheet->setCellValue('C1', '商品价格');
        $sheet->setCellValue('D1', '销量');
        $sheet->setCellValue('E1', '销售额(元)');

        //填充数据
        $index = 0;
        foreach ($list as $key => $item) {
            $sheet->setCellValue('A' . ($index + 2), "\t" . $key . "\t");
            $sheet->setCellValue('B' . ($index + 2), $item['product_name']);
            $sheet->setCellValue('C' . ($index + 2), $item['product_price']);
            $sheet->setCellValue('D' . ($index + 2), $item['total_num']);
            $sheet->setCellValue('E' . ($index + 2), $item['total_price']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '订单') . '-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 日期值过滤
     */
    private function filterTime($value)
    {
        if (!$value) return '';
        return date('Y-m-d H:i:s', $value);
    }

}