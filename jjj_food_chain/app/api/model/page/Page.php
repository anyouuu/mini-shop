<?php

namespace app\api\model\page;

use app\api\model\product\Product as ProductModel;
use app\common\model\page\Page as PageModel;
use app\api\model\plus\coupon\Coupon;
use app\api\model\plus\live\WxLive;
/**
 * 首页模型
 */
class Page extends PageModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * DIY页面详情
     */
    public static function getPageData($user, $page_id = null)
    {
        // 页面详情
        $detail = $page_id > 0 ? parent::detail($page_id) : parent::getHomePage();

        // 页面diy元素
        $items = $detail['page_data']['items'];
        // 页面顶部导航
        isset($detail['page_data']['page']) && $items['page'] = $detail['page_data']['page'];
        // 获取动态数据
        $model = new self;

        foreach ($items as $key => $item) {
            unset($items[$key]['defaultData']);
            if ($item['type'] === 'window') {
                $items[$key]['data'] = array_values($item['data']);
            } else if ($item['type'] === 'product') {
                $items[$key]['data'] = $model->getProductList($user, $item);
            } else if ($item['type'] === 'coupon') {
                $items[$key]['data'] = $model->getCouponList($user, $item, true, 1);
            } else if ($item['type'] === 'wxlive') {
                $items[$key]['data'] = $model->getWxLiveList($item);
            }
        }
        return ['page' => $items['page'], 'items' => $items];
    }

    /**
     * 商品组件：获取商品列表
     */
    private function getProductList($user, $item)
    {
        // 获取商品数据
        $model = new ProductModel;
        if ($item['params']['source'] === 'choice') {
            // 数据来源：手动
            $productIds = array_column($item['data'], 'product_id');
            $productList = $model->getListByIdsFromApi($productIds, $user);
        } else {
            // 数据来源：自动
            $productList = $model->getList([
                'type' => 'sell',
                'category_id' => $item['params']['auto']['category'],
                'sortType' => $item['params']['auto']['productSort'],
                'list_rows' => $item['params']['auto']['showNum'],
            ], $user);
        }
        if ($productList->isEmpty()) return [];
        // 格式化商品列表
        $data = [];
        foreach ($productList as $product) {
            $show_sku = ProductModel::getShowSku($product);
            $data[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'selling_point' => $product['selling_point'],
                'image' => $product['image'][0]['file_path'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $show_sku['product_price'],
                'line_price' => $show_sku['line_price'],
                'product_sales' => $product['product_sales'],
            ];
        }
        return $data;
    }

    /**
     * 优惠券组件：获取优惠券列表
     */
    private function getCouponList($user, $item)
    {
        // 获取优惠券数据
        return (new Coupon)->getList($user, $item['params']['limit'], true);
    }

    /**
     * 微信直播
     */
    private function getWxLiveList($item)
    {
        // 获取头条数据
        $model = new WxLive();
        $liveList = $model->getList($item['params']['showNum']);
        return $liveList->isEmpty() ? [] : $liveList->toArray()['data'];
    }
}