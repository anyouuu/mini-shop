<?php

namespace app\api\controller\coupon;

use app\api\controller\Controller;
use app\api\model\plus\coupon\Coupon as CouponModel;
use app\api\model\product\Product as ProductModel;
/**
 * 优惠券中心
 */
class Coupon extends Controller
{
    /**
     * 优惠券列表
     */
    public function lists()
    {
        $model = new CouponModel;
        $list = $model->getWaitList([], $this->getUser(false), 0, 0);
        return $this->renderSuccess('', compact('list'));
    }

    public function detail($coupon_id){
        $model = CouponModel::detailWithSupplier($coupon_id);
        if($model['apply_range'] == 20){
            $product_ids = explode(',', $model['product_ids']);
            $model['product'] = (new ProductModel())->getListByIdsFromApi($product_ids);
        }
        return $this->renderSuccess('', compact('model'));
    }
}