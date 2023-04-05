<?php


namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\model\user\PointsLog as PointsLogModel;

/**
 * 用户模型
 */
class User extends BaseModel
{
    protected $pk = 'user_id';
    protected $name = 'user';

    /**
     * 生日
     */
    public function getBirthdayAttr($value)
    {
        return $value ? date('Y-m-d', $value) : '';
    }

    /**
     * 关联会员等级表
     */
    public function grade()
    {
        return $this->belongsTo('app\\common\\model\\user\\Grade', 'grade_id', 'grade_id');
    }

    /**
     * 关联收货地址表
     */
    public function address()
    {
        return $this->hasOne('app\\common\\model\\user\\UserAddress', 'address_id', 'address_id');
    }

    /**
     * 关联收货地址表 (默认地址)
     */
    public function addressDefault()
    {
        return $this->belongsTo('app\\common\\model\\user\\UserAddress', 'address_id', 'address_id');
    }

    /**
     * 获取用户信息
     */
    public static function detail($where)
    {
        $model = new static;
        $filter = ['is_delete' => 0];
        if (is_array($where)) {
            $filter = array_merge($filter, $where);
        } else {
            $filter['user_id'] = (int)$where;
        }
        return $model->where($filter)->with(['address', 'addressDefault', 'grade'])->find();
    }

    /**
     * 获取用户信息
     */
    public static function detailByUnionid($unionid)
    {
        $model = new static;
        $filter = ['is_delete' => 0];
        $filter = array_merge($filter, ['union_id' => $unionid]);
        return $model->where($filter)->with(['address', 'addressDefault', 'grade'])->find();
    }

    /**
     * 指定会员等级下是否存在用户
     */
    public static function checkExistByGradeId($gradeId)
    {
        $model = new static;
        return !!$model->where('grade_id', '=', (int)$gradeId)
            ->where('is_delete', '=', 0)
            ->value('user_id');
    }

    /**
     * 累积用户总消费金额
     */
    public function setIncPayMoney($money)
    {
        return $this->where('user_id', '=', $this['user_id'])->inc('pay_money', $money)->update();
    }

    /**
     * 累积用户实际消费的金额 (批量)
     */
    public function onBatchIncExpendMoney($data)
    {
        foreach ($data as $userId => $expendMoney) {
            $this->where(['user_id' => $userId])->inc('expend_money', $expendMoney)->update();
            event('UserGrade', $userId);
        }
        return true;
    }

    /**
     * 累积用户的可用积分数量 (批量)
     */
    public function onBatchIncPoints($data)
    {
        foreach ($data as $userId => $expendPoints) {
            $this->where(['user_id' => $userId])->inc('points', $expendPoints)->update();
        }
        return true;
    }

    /**
     * 累积用户的可用积分
     */
    public function setIncPoints($points, $describe)
    {
        // 新增积分变动明细
        PointsLogModel::add([
            'user_id' => $this['user_id'],
            'value' => $points,
            'describe' => $describe,
            'app_id' => $this['app_id'],
        ]);

        // 更新用户可用积分
        $data['points'] = ($this['points'] + $points <= 0) ? 0 : $this['points'] + $points;
        // 用户总积分
        if ($points > 0) {
            $data['total_points'] = $this['total_points'] + $points;
        }
        $this->where('user_id', '=', $this['user_id'])->update($data);
        event('UserGrade', $this['user_id']);
        return true;
    }

    /**
     * 累计邀请书
     */
    public function setIncInvite($user_id)
    {
        $this->where('user_id', '=', $user_id)->inc('total_invite')->update();
        event('UserGrade', $user_id);
    }
}