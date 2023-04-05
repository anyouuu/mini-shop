<?php

namespace app\shop\controller\cash;

use app\shop\controller\Controller;
use app\shop\model\supplier\Cash as SupplierCashModel;
use app\shop\model\supplier\Supplier as SupplierModel;
use app\shop\model\supplier\Account as SupplierAccountModel;

/**
 * 供应商账号管理控制器
 */
class Account extends Controller
{
    /**
     * 账户管理
     */
    public function index()
    {
        $data = $this->postData();
        $user = $this->store['user'];
        if ($user['user_type'] == 1 || $data['shop_supplier_id'] == 0) {
            $data['shop_supplier_id'] = $user['shop_supplier_id'];
        }
        //门店信息
        $supplier = SupplierModel::detail($data['shop_supplier_id']);
        //提现记录
        $model = new SupplierCashModel;
        $list = $model->getList($data);
        //商家数据
        $supplierList = SupplierModel::getAll();
        return $this->renderSuccess('', compact('list', 'supplierList', 'supplier'));
    }

    /**
     * 保存用户提现账户信息
     */
    public function account($shop_supplier_id)
    {
        $user = $this->store['user'];
        if ($user['user_type'] == 1) {
            $shop_supplier_id = $user['shop_supplier_id'];
        }
        if ($this->request->isGet()) {
            $model = SupplierAccountModel::detail($shop_supplier_id);
            return $this->renderSuccess('', compact('model'));
        }
        $model = new SupplierAccountModel();
        if ($model->add($shop_supplier_id, $this->postData())) {
            return $this->renderSuccess('操作成功', compact('model'));
        }
        return $this->renderError($model->getError() ?: '保存失败');
    }

    /**
     * 申请提现
     */
    public function apply($shop_supplier_id)
    {
        $user = $this->store['user'];
        if ($user['user_type'] == 1) {
            $shop_supplier_id = $user['shop_supplier_id'];
        }
        $model = new SupplierCashModel;
        if ($model->apply($shop_supplier_id, $this->postData())) {
            return $this->renderSuccess('申请提现成功');
        }
        return $this->renderError($model->getError() ?: '提交失败');
    }
}
