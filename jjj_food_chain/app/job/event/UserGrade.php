<?php

namespace app\job\event;

use app\job\model\user\Grade as GradeModel;
use app\job\model\user\User as UserModel;
/**
 * 用户等级事件管理
 */
class UserGrade
{
    /**
     * 执行函数
     */
    public function handle($userId)
    {
        // 设置用户的会员等级
        //$this->setUserGrade($userId);
        return true;
    }

    /**
     * 查询满足会员等级升级条件的用户列表
     */
    public function checkCanUpdate($user, $grade)
    {
        // 按消费升级
        if($grade['open_money'] == 1 && $user['expend_money'] >= $grade['upgrade_money']){
            return true;
        }
        // 按积分升级
        if($grade['open_points'] == 1 && $user['total_points'] >= $grade['upgrade_points']){
            return true;
        }
        // 按消费升级
        if($grade['open_invite'] == 1 && $user['total_invite'] >= $grade['upgrade_invite']){
            return true;
        }
        return false;
    }

    /**
     * 记录日志
     */
    private function dologs($method, $params = [])
    {
        $value = 'UserGrade --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value);
    }
}
