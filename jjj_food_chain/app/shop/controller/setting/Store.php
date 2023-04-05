<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Setting as SettingModel;
use app\common\enum\settings\DeliveryTypeEnum;

/**
 * 商城设置控制器
 */
class Store extends Controller
{
    /**
     * 商城设置
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        $data = $this->request->param();
        $arr = [
            'name' => $data['name'],
            'delivery_type' => $data['checkedCities'],
            'kuaidi100' => ['customer' => $data['customer'], 'key' => $data['key']],
            'supplier_cash' => $data['supplier_cash'],
            'commission_rate' => $data['commission_rate'],
            'add_audit' => $data['add_audit'],
            'edit_audit' => $data['edit_audit'],
            'supplier_image' => $data['supplier_image'],
            'sms_open' => $data['sms_open'],
            'supplier_logo' => $data['supplier_logo'],
            'is_get_log' => $data['is_get_log'],
        ];
        if ($model->edit('store', $arr)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 获取商城配置
     */
    public function fetchData()
    {
        $vars['values'] = SettingModel::getItem('store');
        $all_type = DeliveryTypeEnum::data();
        return $this->renderSuccess('', compact('vars', 'all_type'));
    }

}
