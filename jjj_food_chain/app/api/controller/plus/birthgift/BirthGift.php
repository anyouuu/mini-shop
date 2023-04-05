<?php

namespace app\api\controller\plus\birthgift;

use app\api\controller\Controller;
use app\api\model\plus\birthgift\BirthGift as BirthGiftModel;

/**
 * 生日有礼控制器
 */
class BirthGift extends Controller
{
    //获取数据
    public function index()
    {
        $model = new BirthGiftModel;
        $list = $model->getDetail($this->getUser(false));
        return $this->renderSuccess('', compact('list'));
    }

    //领取礼包
    public function receive($gift_id)
    {
        $model = new BirthGiftModel;
        if ($model->receive($gift_id, $this->getUser())) {
            return $this->renderSuccess('领取成功');
        }
        return $this->renderError($model->getError() ?: '领取成功');
    }
}