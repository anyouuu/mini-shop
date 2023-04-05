<?php

namespace app\api\controller\product;

use app\api\model\product\Product as ProductModel;
use app\api\model\order\Cart as CartModel;
use app\api\controller\Controller;
use app\api\model\settings\Setting as SettingModel;
use app\api\model\user\Visit as VisitModel;
use app\api\service\common\RecommendService;
use app\api\model\user\Favorite as FavoriteModel;
use app\api\model\plus\coupon\Coupon as CouponModel;

/**
 * 商品控制器
 */
class Product extends Controller
{
    /**
     * 商品列表
     */
    public function lists()
    {
        // 整理请求的参数
        $param = array_merge($this->postData(), [
            'product_status' => 10,
        ]);

        // 获取列表数据
        $model = new ProductModel;
        $list = $model->getList($param, $this->getUser(false));
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 推荐产品
     */
    public function recommendProduct($location)
    {
        $recommend = SettingModel::getItem('recommend');
        $model = new ProductModel;
        $is_recommend = RecommendService::checkRecommend($recommend, $location);
        $list = [];
        if ($is_recommend) {
            $list = $model->getRecommendProduct($recommend);
        }
        return $this->renderSuccess('', compact('list', 'recommend', 'is_recommend'));
    }

    /**
     * 获取商品详情
     */
    public function detail($product_id, $url)
    {
        $params = $this->postData();
        // 用户信息
        $user = $this->getUser(false);
        // 商品详情
        $model = new ProductModel;
        $product = $model->getDetails($product_id, $this->getUser(false));
        if ($product === false) {
            return $this->renderError($model->getError() ?: '商品信息不存在');
        }
        // 多规格商品sku信息
        $specData = $product['spec_type'] == 20 ? $model->getManySpecData($product['spec_rel'], $product['sku']) : null;
        $isfollow = 0;
        if ($user) {
            if (FavoriteModel::detail($product_id, 20, $user['user_id'])) {
                $isfollow = 1;
            }
        }
        $product['isfollow'] = $isfollow;
        $dataCoupon['shop_supplier_id'] = $product['shop_supplier_id'];
        $model = new CouponModel;
        $couponList = $model->getWaitList($dataCoupon, $this->getUser(false), 1);
        // 访问记录
        (new VisitModel())->addVisit($user, $product['supplier'], $params['visitcode'], $product);
        return $this->renderSuccess('', [
            // 商品详情
            'detail' => $product,
            // 购物车商品总数量
            'cart_total_num' => $user ? (new CartModel($user))->getProductNum() : 0,
            // 多规格商品sku信息
            'specData' => $specData,
            // 微信公众号分享参数
            'share' => $this->getShareParams($url, $product['product_name'], $product['product_name'], '/pages/product/detail/detail', $product['image'][0]['file_path']),
            'couponList' => $couponList,
            //是否显示店铺信息
            'store_open' => SettingModel::getItem('nav')['data']['list'][2]['is_show'] ? 1 : 0,
            //是否开启客服
            'service_open' => SettingModel::getSysConfig()['service_open'],
        ]);
    }
}