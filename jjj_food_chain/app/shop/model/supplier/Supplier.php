<?php

namespace app\shop\model\supplier;

use app\common\model\supplier\Supplier as SupplierModel;
use app\shop\model\product\Product as ProductModel;
use app\shop\model\auth\User as ShopUserModel;

/**
 * 后台管理员登录模型
 */
class Supplier extends SupplierModel
{
    /**
     * 获取列表数据
     */
    public function getList($params, $user)
    {
        $model = $this;
        if ($user['user_type'] == 1) {
            $model = $model->where('shop_supplier_id', '=', $user['shop_supplier_id']);
        }
        // 查询列表数据
        return $model->with(['superUser'])
            ->where('is_delete', '=', '0')
            ->order(['create_time' => 'desc'])
            ->paginate($params);
    }

    /**
     * 添加
     */
    public function add($data)
    {
        // 开启事务
        $this->startTrans();
        try {
            $supplier = $data['supplier'];
            $num = (new ShopUserModel)->getSupplierUserName(['user_name' => $supplier['user_name']]);
            if ($num > 0) {
                $this->error = '用户名已存在';
                return false;
            }
            $coordinate = explode(',', $supplier['coordinate']);
            $supplier['latitude'] = $coordinate[0];
            $supplier['longitude'] = $coordinate[1];
            $supplier['delivery_set'] = [10, 20];
            $supplier['store_set'] = [30, 40];
            // 添加供应商
            $supplier['app_id'] = self::$app_id;
            $this->save($supplier);
            // 添加登录用户
            $add = [
                'user_id' => $supplier['user_id'],
                'user_name' => $supplier['user_name'],
                'password' => salt_hash($supplier['password']),
                'real_name' => $supplier['user_name'],
                'shop_supplier_id' => $this['shop_supplier_id'],
                'is_super' => 1,
                'user_type' => 1,
                'app_id' => self::$app_id,
            ];
            //添加管理员账号
            (new ShopUserModel)->save($add);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 修改
     */
    public function edit($data)
    {
        // 开启事务
        $this->startTrans();
        try {
            $supplier = $data['supplier'];
            $num = (new ShopUserModel)->getSupplierUserName(['user_name' => $supplier['user_name']],$this['superUser']['shop_user_id']);
            if ($num > 0) {
                $this->error = '用户名已存在';
                return false;
            }
            $coordinate = explode(',', $supplier['coordinate']);
            $supplier['latitude'] = $coordinate[0];
            $supplier['longitude'] = $coordinate[1];
            // 修改供应商
            $this->save($supplier);
            // 修改登录用户
            $user_model = $this['superUser'];
            $user_data = [
                'user_name' => $supplier['user_name']
            ];
            if (isset($supplier['password']) && !empty($supplier['password'])) {
                $user_data['password'] = salt_hash($supplier['password']);
            }
            $user_model->save($user_data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 修改
     */
    public function setting($data)
    {
        // 开启事务
        $this->startTrans();
        try {
            $data['delivery_time'] = json_encode([$data['delivery_time'][0], $data['delivery_time'][1]]);
            $data['store_time'] = json_encode([$data['store_time'][0], $data['store_time'][1]]);
            $data['pick_time'] = json_encode([$data['pick_time'][0], $data['pick_time'][1]]);
            // 修改
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

    /**
     * 开启禁止
     */
    public function setRecycle($is_recycle)
    {
        // 开启事务
        $this->startTrans();
        try {
            if ($is_recycle == 1) {
                //产品下架
                ProductModel::where('shop_supplier_id', '=', $this['shop_supplier_id'])->update(['product_status' => 20]);
            }
            //更改店铺状态
            $this->save(['is_recycle' => $is_recycle]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 获取列表数据
     */
    public static function getAll()
    {
        $model = new static();
        // 查询列表数据
        return $model->field(['shop_supplier_id,name'])->where('is_delete', '=', '0')
            ->order(['create_time' => 'asc'])
            ->select();
    }


    /**
     * 提现驳回：解冻资金
     */
    public static function backFreezeMoney($shop_supplier_id, $money)
    {
        $model = self::detail($shop_supplier_id);
        return $model->save([
            'money' => $model['money'] + $money,
            'freeze_money' => $model['freeze_money'] - $money,
        ]);
    }

    /**
     * 提现打款成功：累积提现金额
     */
    public static function totalMoney($shop_supplier_id, $money)
    {
        $model = self::detail($shop_supplier_id);
        return $model->save([
            'freeze_money' => $model['freeze_money'] - $money,
            'cash_money' => $model['cash_money'] + $money,
        ]);
    }

    /**
     * 获取供应商数量
     */
    public static function getTotal($where)
    {
        $model = new static;
        return $model->where($where)->count();
    }

    /**
     * 获取供应商总数量
     */
    public function getSupplierTotal()
    {
        return $this->where(['is_delete' => 0])->count();
    }

    /**
     * 获取供应商总数量
     */
    public static function getSupplierTotalByDay($day)
    {
        $startTime = strtotime($day);
        return self::where('create_time', '>=', $startTime)
            ->where('create_time', '<', $startTime + 86400)
            ->count();
    }

    /**
     * 获取供应商统计数量
     */
    public function getSupplierData($startDate = null, $endDate = null, $type)
    {
        $model = $this;
        if (!is_null($startDate)) {
            $model = $model->where('create_time', '>=', strtotime($startDate));
        }
        if (is_null($endDate)) {
            $model = $model->where('create_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('create_time', '<', strtotime($endDate) + 86400);
        }
        if ($type == 'supplier_total' || $type == 'supplier_add') {
            return $model->count();
        }
        return 0;
    }

    /**
     * 获取平台的总销售额
     */
    public function getTotalMoney($type = 'total_money')
    {
        $model = $this;
        if ($type == 'total') {
            return $model->sum('total_money');
        } else if ($type == 'money') {
            return $model->sum('money');
        } else if ($type == 'freeze_money') {
            return $model->sum('freeze_money');
        } else if ($type == 'cash_money') {
            return $model->sum('cash_money');
        } else if ($type == 'deposit_money') {
            return $model->sum('deposit_money');
        }
        return 0;
    }
}