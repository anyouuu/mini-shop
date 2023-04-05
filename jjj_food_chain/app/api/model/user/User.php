<?php

namespace app\api\model\user;

use think\facade\Cache;
use app\common\exception\BaseException;
use app\common\model\user\User as UserModel;
use app\common\library\easywechat\AppWx;
use app\common\model\user\Grade as GradeModel;
use app\common\library\wechat\WxBizDataCrypt;

/**
 * 用户模型类
 */
class User extends UserModel
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
     * 获取用户信息
     */
    public static function getUser($token)
    {
        $userId = Cache::get($token);
        return self::where(['user_id' => $userId])->with(['address', 'addressDefault', 'grade'])->find();
    }

    /**
     * 用户登录
     */
    public function login($post)
    {
        // 微信登录 获取session_key
        $app = AppWx::getApp();
        $session = $app->auth->session($post['code']);
        $userInfo = json_decode(htmlspecialchars_decode($post['user_info']), true);

        $reg_source = $post['source'];
        $user_id = $this->register($session['openid'], $userInfo, $session, $reg_source);
        // 生成token (session3rd)
        $this->token = $this->token($session['openid']);
        // 记录缓存, 7天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
        return $user_id;
    }

    /**
     * 用户登录
     */
    public function bindMobile($post)
    {
        // 微信登录 获取session_key
        $app = AppWx::getApp();
        $session = $app->auth->session($post['code']);
        $iv = urldecode($post['iv']);
        $encrypted_data = urldecode($post['encrypted_data']);
        $pc = new WxBizDataCrypt($app['config']['app_id'], $session['session_key']);
        $errCode = $pc->decryptData($encrypted_data, $iv, $data);
        if ($errCode == 0) {
            $data = json_decode($data, true);
            return $this->save([
                'mobile' => $data['phoneNumber']
            ]);
        }
        return false;
    }

    /**
     * 获取token
     */
    public function getToken()
    {
        return $this->token;
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

    /**
     * 自动注册用户
     */
    private function register($open_id, $data, $decryptedData = [], $reg_source = '')
    {
        //通过unionid查询用户是否存在
        $user = null;
        if (isset($decryptedData['unionid']) && !empty($decryptedData['unionid'])) {
            $data['union_id'] = $decryptedData['unionid'];
            $user = self::detailByUnionid($decryptedData['unionid']);
        }
        if (!$user) {
            // 通过open_id查询用户是否已存在
            $user = self::detail(['open_id' => $open_id]);
        }
        if ($user) {
            $model = $user;
        } else {
            $model = $this;
            $data['reg_source'] = 'wx';
            //默认等级
            $data['grade_id'] = GradeModel::getDefaultGradeId();
        }
        $this->startTrans();
        try {
            // 保存/更新用户记录
            if (!$model->save(array_merge($data, [
                'open_id' => $open_id,
                'app_id' => self::$app_id
            ]))
            ) {
                throw new BaseException(['msg' => '用户注册失败']);
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }
        return $model['user_id'];
    }

    /**
     * 签到更新用户积分
     */
    public function setPoints($user_id, $days, $sign_conf, $sign_date)
    {
        $rank = $sign_conf['ever_sign'];
        if ($sign_conf['is_increase'] == 'true') {
            if ($days >= $sign_conf['no_increase']) {
                $days = $sign_conf['no_increase'] - 1;
            }
            $rank = ($days - 1) * $sign_conf['increase_reward'] + $rank;
        }
        //是否奖励
        if (isset($sign_conf['reward_data'])) {
            $arr = array_column($sign_conf['reward_data'], 'day');
            if (in_array($days, $arr)) {
                $key = array_search($days, $arr);
                if ($sign_conf['reward_data'][$key]['is_point'] == 'true') {
                    $rank = $sign_conf['reward_data'][$key]['point'] + $rank;
                }
            }
        }
        // 新增积分变动明细
        $this->setIncPoints($rank, '用户签到：签到日期' . $sign_date);
        return $rank;
    }

    //修改个人资料
    public function updateInfo($data)
    {
        $data['birthday'] = strtotime($data['birthday']);
        return $this->save($data);
    }

}
