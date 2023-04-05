<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\api\model\settings\Setting as SettingModel;
use app\api\model\user\UserOpen as UserOpenModel;
use app\api\model\user\UserMp as UserMpModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\library\easywechat\AppMp;
use app\common\model\app\AppOpen as AppOpenModel;
use app\common\model\user\Sms as SmsModel;
/**
 * app用户管理
 */
class Useropen extends Controller
{
    /**
     * 用户自动登录
     */
    public function login($referee_id = '', $code)
    {
        $wxConfig = AppOpenModel::getAppOpenCache($this->app_id);
        $appId = $wxConfig['openapp_id'];
        $appSecret = $wxConfig['openapp_secret'];
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appId . '&secret=' . $appSecret . '&code=' . $code . '&grant_type=authorization_code';

        $stream_opts = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];
        //获取token，为了获取access_token 如果没有就弹出错误
        $token = json_decode(file_get_contents($token_url, false, stream_context_create($stream_opts)));
        if (isset($token->errcode)) {
            log_write($token->errcode);
            log_write($token->errmsg);
            return $this->renderError($token->errmsg);
        }
        $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appId . '&grant_type=refresh_token&refresh_token=' . $token->refresh_token;
        //获取access_token ，为了获取微信的个人信息，如果没有就弹出错误
        $access_token = json_decode(file_get_contents($access_token_url, false, stream_context_create($stream_opts)));
        if (isset($access_token->errcode)) {
            return $this->renderError($access_token->errmsg);
        }
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
        //获取用户信息
        $user_info = json_decode(file_get_contents($user_info_url, false, stream_context_create($stream_opts)));
        if (isset($user_info->errcode)) {
            log_write($user_info->errcode);
            log_write($user_info->errmsg);
            return $this->renderError($user_info->errmsg);
        }
        $model = new UserOpenModel;
        $user_id = $model->login((array)$user_info, $referee_id);
        return $this->renderSuccess('',[
            'user_id' => $user_id,
            'token' => $model->getToken()
        ]);
    }

    public function payWx($order_id){
        $user = $this->getUser();
        // 订单详情
        $model = OrderModel::getUserOrderDetail($order_id, $user['user_id']);
        // 构建支付请求
        $payment = OrderModel::onOrderPayment($user, [$model], OrderPayTypeEnum::WECHAT, 'payApp');

        return $this->renderSuccess('',[
            'order' => $model,  // 订单详情
            'payment' => $payment
        ]);
    }

    public function invite($referee_id = ''){
        $app = AppMp::getApp($this->app_id);
        $redirect_uri = base_url()."index.php/api/user.useropen/invite_callback?app_id={$this->app_id}&referee_id={$referee_id}";
        $app->oauth->scopes(['snsapi_userinfo'])->redirect($redirect_uri)->send();
    }

    /**
     * 用户自动登录
     */
    public function invite_callback()
    {
        $app = AppMp::getApp($this->app_id);
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $userInfo = $oauth->user();
        // 绑定关系,保存数据库
        $model = new UserMpModel;
        $referee_id = $this->request->param('referee_id');
        $model->login($userInfo, $referee_id);
        //跳转到app下载页
        $appshare = SettingModel::getItem('appshare');
        $down_url = $appshare['down_url'];
        return redirect($down_url);
    }
    /**
     * 获取登录logo
    */
    public function logo(){
        // 当前app信息
        $logo = AppOpenModel::detail()['logo'];
        return $this->renderSuccess('', compact('logo'));
    }
    /**
     * 手机号码登录
    */
    public function phonelogin(){
        $data = $this->request->post();
        $model = new UserOpenModel;
        $user_id = $model->phoneLogin($data);
        if($user_id){
            return $this->renderSuccess('',[
            'user_id' => $user_id,
            'token' => $model->getToken()
        ]);
        }
        return $this->renderError($model->getError() ?:'登录失败');
    }
    /**
     * 短信登录
    */
    public function smslogin(){
        $data = $this->request->post();
        $model = new UserOpenModel;
        $user_id = $model->smslogin($data);
        if($user_id){
            return $this->renderSuccess('',[
            'user_id' => $user_id,
            'token' => $model->getToken()
        ]);
        }
        return $this->renderError($model->getError() ?:'登录失败');
    }
    /**
     * 忘记密码
    */
    public function resetpassword(){
        $data = $this->request->post();
        $model = new UserOpenModel;
        if($model->resetpassword($data)){
            return $this->renderSuccess('设置成功');
        }
        return $this->renderError($model->getError() ?:'设置失败');
    }
    /**
     * 手机号码注册
    */
    public function register(){
        $data = $this->request->post();
        $model = new UserOpenModel;
        if($model->phoneRegister($data)){
            return $this->renderSuccess('注册成功');
        }
        return $this->renderError($model->getError() ?:'注册失败');

    }
    /**
     * 发送短信
     */
    public function sendCode($mobile,$type)
    {
        $model = new SmsModel();
        if($model->send($mobile, $type)){
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError() ?:'发送失败');
    }
}
