<?php

namespace app\api\controller\user;

use app\api\model\user\User as UserModel;
use app\api\controller\Controller;
use app\api\model\settings\Setting as SettingModel;

/**
 * 用户管理模型
 */
class User extends Controller
{
    /**
     * 用户自动登录,默认微信小程序
     */
    public function login()
    {
        $model = new UserModel;
        $user_id = $model->login($this->request->post());
        return $this->renderSuccess('', [
            'user_id' => $user_id,
            'token' => $model->getToken()
        ]);
    }

    /**
     * 当前用户详情
     */
    public function detail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        $gift_name = SettingModel::getItem('live')['gift_name'];
        return $this->renderSuccess('', compact('userInfo', 'gift_name'));
    }

    /**
     * 绑定手机号
     */
    public function bindMobile()
    {
        $model = $this->getUser();
        if ($model->bindMobile($this->request->post())) {
            return $this->renderSuccess('');
        }
        return $this->renderError('绑定失败');
    }

    /**
     * 修改用户资料
     */
    public function updateInfo()
    {
        $model = $this->getUser();
        if ($model->updateInfo($this->request->post())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }
}