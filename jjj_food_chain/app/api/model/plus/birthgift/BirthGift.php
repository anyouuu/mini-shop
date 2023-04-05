<?php


namespace app\api\model\plus\birthgift;

use app\common\model\plus\birthgift\BirthGift as BirthGiftModel;
use app\api\model\plus\coupon\UserCoupon;
use app\api\model\plus\coupon\Coupon as CouponModel;

/**
 * 生日有礼模型
 */
class BirthGift extends BirthGiftModel
{
    //修改
    public function getDetail($user = false)
    {
        $model = $this;
        $detail = $model->where('status', '=', 1)->find();
        if ($detail['coupon_ids']) {
            $detail['coupon'] = (new CouponModel)->getGiftList($detail['coupon_ids']);
        } else {
            $detail['coupon'] = [];
        }
        //领取时间
        $rtime = strtotime(date('Y', $user['receive_time']));
        //当前时间
        $ntime = strtotime(date('Y', time()));
        if ($user && $user['receive_time'] && $rtime == $ntime) {
            $detail['is_receive'] = 1;
        } else {
            $detail['is_receive'] = 0;
        }
        return $detail;
    }

    //领取生日礼包
    public function receive($gift_id, $user)
    {
        $this->startTrans();
        try {
            $detail = $this->where('gift_id', '=', $gift_id)
                ->where('status', '=', 1)
                ->find();
            if (!$detail) {
                $this->error = '礼包不存在';
                return false;
            }
            if ($user && !$user['birthday']) {
                $this->error = '请设置生日';
                return false;
            }
            //领取时间
            $rtime = $user['receive_time'] ? strtotime(date('Y', $user['receive_time'])) : 0;
            //当前时间
            $ntime = strtotime(date('Y', time()));
            //当前日期
            $mtime = strtotime(date('Y-m-d', time()));
            //生日
            $btime = $user['birthday'] ? strtotime(date(date('Y') . '-m-d', strtotime($user['birthday']))) : 0;
            if ($user && $user['birthday'] && $mtime != $btime) {
                $this->error = '生日未到，请耐心等待';
                return false;
            }
            if ($user && $user['receive_time'] && $rtime == $ntime) {
                $this->error = '礼包已领取';
                return false;
            }
            if ($detail['coupon_ids']) {
                $UserCouponModel = new UserCoupon;
                $UserCouponModel->addUserCoupon($detail['coupon_ids'], $user['user_id']);
            }
            $user->save(['receive_time' => time()]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}