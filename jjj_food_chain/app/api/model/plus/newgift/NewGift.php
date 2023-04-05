<?php


namespace app\api\model\plus\newgift;

use app\common\model\plus\newgift\NewGift as NewGiftModel;
use app\api\model\plus\coupon\UserCoupon;
use app\api\model\plus\coupon\Coupon as CouponModel;

/**
 * 新人有礼模型
 */
class NewGift extends NewGiftModel
{
    //修改
    public function getlist($shop_supplier_id, $user = false)
    {
        $model = $this;
        if ($shop_supplier_id) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        $list = $model->with(['supplier'])
            ->where('status', '=', 1)
            ->where('start_time', '<', time())
            ->where('end_time', '>', time())
            ->select();
        foreach ($list as $key => &$item) {
            if ($item['coupon_ids'] && $item['is_coupon']) {
                $item['coupon'] = (new CouponModel)->getGiftList($item['coupon_ids']);
            } else {
                $item['coupon'] = [];
            }
            $user['gift_supplier_id'] && $user['gift_supplier_id'] = explode(',', $user['gift_supplier_id']);
            if ($user && $user['gift_supplier_id'] && in_array($item['shop_supplier_id'], $user['gift_supplier_id'])) {
                $item['is_receive'] = 1;
            } else {
                $item['is_receive'] = 0;
            }
        }
        return $list;
    }

    //领取新人礼包
    public function receive($gift_id, $user)
    {
        $this->startTrans();
        try {
            $detail = $this->where('gift_id', '=', $gift_id)
                ->where('status', '=', 1)
                ->where('start_time', '<', time())
                ->where('end_time', '>', time())
                ->find();
            if (!$detail) {
                $this->error = '礼包不存在';
                return false;
            }
            $user['gift_supplier_id'] && $user['gift_supplier_id'] = explode(',', $user['gift_supplier_id']);
            if ($user && $user['gift_supplier_id'] && in_array($detail['shop_supplier_id'], $user['gift_supplier_id'])) {
                $this->error = '礼包已领取';
                return false;
            }
            if ($detail['is_point'] && $detail['points']) {
                $user->setIncPoints($detail['points'], '新人礼包获取积分');
            }
            if ($detail['is_coupon'] && $detail['coupon_ids']) {
                $UserCouponModel = new UserCoupon;
                $UserCouponModel->addUserCoupon($detail['coupon_ids'], $user['user_id']);
            }
            if ($user['gift_supplier_id']) {
                $user['gift_supplier_id'] = array_merge($user['gift_supplier_id'], [$detail['shop_supplier_id']]);
                $user->save(['gift_supplier_id' => implode(',', $user['gift_supplier_id'])]);
            } else {
                $user->save(['gift_supplier_id' => $detail['shop_supplier_id']]);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}