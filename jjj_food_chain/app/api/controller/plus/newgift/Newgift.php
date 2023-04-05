<?php

namespace app\api\controller\plus\newgift;

use app\api\controller\Controller;
use app\api\model\plus\newgift\NewGift as NewGiftModel;

/**
 * 新人有礼到控制器
 */
class Newgift extends Controller
{
    //获取数据
    public function index($shop_supplier_id = 0)
    {
        $model = new NewGiftModel;
        $list = $model->getlist($shop_supplier_id, $this->getUser(false));
        return $this->renderSuccess('', compact('list'));
    }

    //领取礼包
    public function receive($gift_id)
    {
        $model = new NewGiftModel;
        if ($model->receive($gift_id, $this->getUser())) {
            return $this->renderSuccess('领取成功');
        }
        return $this->renderError($model->getError() ?: '领取成功');
    }
}