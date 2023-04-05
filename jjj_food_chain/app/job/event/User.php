<?php

namespace app\job\event;

use app\job\model\user\User as UserModel;
use app\common\library\helper;

/**
 * 用户事件管理
 */
class User
{
    /**
     * 执行函数
     */
    public function handle()
    {
        // 发送会员生日信息
        $this->setUserBirthSms();
        return true;
    }

    /**
     * 发送会员生日信息
     */
    private function setUserBirthSms()
    {
        $detail = (new UserModel)->getDetail();
        if (!$detail || ($detail && $detail['sms_open'] == 0)) {
            return false;
        }
        //当前时间大于8点发送
        $sendTime = strtotime(date('Y-m-d 8:00:00'));
        if (time() < $sendTime) {
            return false;
        }
        // 获取所有生日用户
        $list = (new UserModel)->getBirthList();
        if ($list->isEmpty()) {
            return false;
        }
        (new UserModel)->sendSms($list);
        // 用户id集
        $userIds = helper::getArrayColumn($list, 'user_id');
        //发送生日短信
        // 记录日志
        $this->dologs('userBirth', [
            'userIds' => json_encode($userIds),
        ]);
    }

    /**
     * 记录日志
     */
    private function dologs($method, $params = [])
    {
        $value = 'userBirth --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value);
    }
}
