<?php

namespace app\shop\controller\supplier;

use app\shop\controller\Controller;
use app\shop\model\supplier\Supplier as SupplierModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\shop\model\supplier\Capital as CapitalModel;
use app\shop\model\supplier\Cash as SupplierCashModel;
use app\common\service\qrcode\SupplierService;

/**
 * 供应商控制器
 */
class Supplier extends Controller
{

    /**
     * 店员列表
     */
    public function index()
    {
        // 供应商列表
        $model = new SupplierModel;
        $list = $model->getList($this->postData(), $this->store['user']);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 添加供应商
     */
    public function add()
    {
        $model = new SupplierModel;
        // 新增记录
        if ($model->add($this->postData())) {
            return $this->renderSuccess('', '添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑供应商
     */
    public function edit($shop_supplier_id)
    {
        $model = SupplierModel::detail($shop_supplier_id, ['superUser']);
        if ($this->request->isGet()) {
            $model['deliver_set'] = array_keys(DeliveryTypeEnum::data());
            return $this->renderSuccess('', compact('model'));
        }
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('', '更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除
     */
    public function delete($shop_supplier_id)
    {
        // 店员详情
        $model = SupplierModel::detail($shop_supplier_id);
        if (!$model->setDelete()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('', $model->getError() ?: '删除成功');
    }

    /**
     * 开启禁止
     */
    public function recycle($shop_supplier_id, $is_recycle)
    {
        // 商品详情
        $model = SupplierModel::detail($shop_supplier_id);
        if (!$model->setRecycle($is_recycle)) {
            return $this->renderError('操作失败');
        }
        return $this->renderSuccess('操作成功');
    }

    /**
     * 设置门店
     */
    public function setting($shop_supplier_id)
    {
        $model = SupplierModel::detail($shop_supplier_id, []);
        if ($this->request->isGet()) {
            return $this->renderSuccess('', compact('model'));
        }
        if ($model->setting($this->postData())) {
            return $this->renderSuccess('', '更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 余额明细
     */
    public function balance()
    {
        $model = new CapitalModel;
        return $this->renderSuccess('', [
            // 余额记录
            'list' => $model->getList($this->postData('Params'), $this->store['user']),
        ]);
    }

    /**
     * 提现列表
     */
    public function cash()
    {
        $model = new SupplierCashModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 提现审核
     */
    public function submit($id)
    {
        $model = SupplierCashModel::detail($id);
        if ($model->submit($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 确认打款
     */
    public function money($id)
    {
        $model = SupplierCashModel::detail($id);

        if ($model->money()) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 二维码
     */
    public function qrcode($shop_supplier_id, $source)
    {
        $Qrcode = new SupplierService($shop_supplier_id, $source);
        $Qrcode->getImage();
    }
}
