<?php

namespace app\admin\controller\admin;

use app\admin\controller\Controller;
use app\admin\model\admin\User as AdminUserModel;

/**
 * 超管后台管理员控制器
 */
class User extends Controller
{
    /**
     * 更新当前管理员信息
     */
    public function renew()
    {
        $session = session('jjjshop_admin');
        $model = AdminUserModel::detail($session['user']['admin_user_id']);
        if ($model->renew($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?:'更新失败');
    }
}