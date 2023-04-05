<?php

namespace app\job\model\user;

use app\common\model\user\User as UserModel;
use app\job\model\user\GradeLog as GradeLogModel;
use app\common\enum\user\grade\ChangeTypeEnum;
use app\common\service\message\MessageService;
use app\common\model\plus\birthgift\BirthGift as BirthGiftModel;

/**
 * 用户模型
 */
class User extends UserModel
{
    /**
     * 批量设置会员等级
     */
    public function upgradeGrade($user, $upgradeGrade)
    {
        // 更新会员等级的数据
        $this->where('user_id', '=', $user['user_id'])
            ->update([
                'grade_id' => $upgradeGrade['grade_id']
            ]);
        (new GradeLogModel)->save([
            'old_grade_id' => $user['grade_id'],
            'new_grade_id' => $upgradeGrade['grade_id'],
            'change_type' => ChangeTypeEnum::AUTO_UPGRADE,
            'user_id' => $user['user_id'],
            'app_id' => $user['app_id']
        ]);
        return true;
    }

    /**
     * 获取生日会员
     */
    public function getBirthList()
    {
        $birthSql = "UNIX_TIMESTAMP(concat(YEAR(NOW()),FROM_UNIXTIME(birthday,'-%m-%d')))-UNIX_TIMESTAMP(FROM_UNIXTIME(unix_timestamp(now()),'%Y-%m-%d'))=86400";
        $sendSql = "YEAR(FROM_UNIXTIME(send_time,'%Y-%m-%d'))<>YEAR(now())";
        $list = UserModel::where('is_delete', '=', 0)->where($birthSql)->where($sendSql)->limit(10)->select();
        return $list;
    }

    /**
     * 发送短信
     */
    public function sendSms($list)
    {
        foreach ($list as $item) {
            (new MessageService)->birthSms($item);
        }
    }

    /**
     * 生日有礼详情
     */
    public function getDetail()
    {
        return BirthGiftModel::where('gift_id','<>',0)->find();
    }

}
