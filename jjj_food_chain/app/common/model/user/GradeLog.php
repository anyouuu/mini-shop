<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

/**
 * 用户会员等级变更记录模型
 */
class GradeLog extends BaseModel
{
    protected $name = 'user_grade_log';
    protected $pk = 'log_id';
    protected $updateTime = false;

}