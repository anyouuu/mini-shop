<?php

namespace app\api\model\user;

use app\common\model\user\Grade as GradeModel;
use app\common\model\user\Sms as SmsModel;
use think\facade\Cache;
use app\common\model\user\User as UserModel;

/**
 * 用户模型类
 */
class UserWeb extends UserModel
{
    private $token;

    /**
     * 隐藏字段
     */
    protected $hidden = [
        'open_id',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 用户登录
     */
    public function login($data)
    {
        if(!$this->check($data)){
            return false;
        }
        $user = $this->where('mobile', '=', $data['mobile'])->find();
        if(!$user){
            $this->save([
                'mobile' => $data['mobile'],
                'reg_source' => 'h5',
                //默认等级
                'grade_id' => GradeModel::getDefaultGradeId(),
                'app_id' => self::$app_id
            ]);
            $user_id = $this['user_id'];
            $mobile = $data['mobile'];
        }else{
            $user_id = $user['user_id'];
            $mobile = $user['mobile'];
        }
        // 生成token (session3rd)
        $this->token = $this->token($mobile);
        // 记录缓存, 30天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 30);
        return $user_id;
    }

    /**
     * 验证
     */
    private function check($data)
    {
        //判断验证码是否过期、是否正确
        $sms_model = new SmsModel();
        $sms_record_list = $sms_model
            ->where('mobile', '=', $data['mobile'])
            ->order(['create_time' => 'desc'])
            ->limit(1)->select();

        if(count($sms_record_list) == 0){
            $this->error = '未查到短信发送记录';
            return false;
        }
        $sms_model = $sms_record_list[0];
        if((time() - strtotime($sms_model['create_time']))/60 > 30){
            $this->error = '短信验证码超时';
            return false;
        }
        if($sms_model['code'] != $data['code']){
            $this->error = '验证码不正确';
            return false;
        }
        return true;
    }

    /**
     * 获取token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 绑定手机
     */
    public function bindMobile($user, $data){
        if(!$this->check($data)){
            return false;
        }
        return $user->save([
            'mobile' => $data['mobile']
        ]);
    }

    /**
     * 生成用户认证的token
     */
    private function token($openid)
    {
        $app_id = self::$app_id;
        // 生成一个不会重复的随机字符串
        $guid = \getGuidV4();
        // 当前时间戳 (精确到毫秒)
        $timeStamp = microtime(true);
        // 自定义一个盐
        $salt = 'token_salt';
        return md5("{$app_id}_{$timeStamp}_{$openid}_{$guid}_{$salt}");
    }

}
