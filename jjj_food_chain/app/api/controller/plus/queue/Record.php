<?php

namespace app\api\controller\plus\queue;

use app\api\controller\Controller;
use app\api\model\plus\queue\QueueRecord as QueueRecordModel;
use app\api\model\plus\queue\QueueTable as QueueTableModel;
use app\api\model\plus\queue\QueueSetting as QueueSettingModel;

/**
 * 用户排队取号到控制器
 */
class Record extends Controller
{
    //获取取号数据
    public function index($shop_supplier_id)
    {
        $model = new QueueTableModel;
        //桌位列表
        $tableList = $model->getAllList($shop_supplier_id);
        //桌位人数列表
        $tableNum = $model->getAllNum($shop_supplier_id);
        //取号规则
        $rule = QueueSettingModel::detail($shop_supplier_id);
        return $this->renderSuccess('', compact('tableList', 'tableNum', 'rule'));
    }

    //获取取号数据
    public function take()
    {
        $model = new QueueRecordModel;
        if ($model->take($this->postData(), $this->getUser())) {
            return $this->renderSuccess('取号成功');
        }
        return $this->renderError($model->getError() ?: '取号失败');
    }

    //获取我的取号记录
    public function list()
    {
        $model = new QueueRecordModel;
        $list = $model->getList($this->postData(), $this->getUser());
        return $this->renderSuccess('', compact('list'));
    }
}