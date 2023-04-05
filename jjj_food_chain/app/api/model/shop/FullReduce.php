<?php

namespace app\api\model\shop;

use app\common\library\helper;
use app\common\model\shop\FullReduce as FullReduceModel;

/**
 * 满减模型
 */
class FullReduce extends FullReduceModel
{
    /**
     * 获取列表记录
     */
    public static function getReductList($total_price, $total_num, $shop_supplier_id)
    {
        // 获取所有满减活动
        $list = (new self)->getAll($shop_supplier_id);
        $data = [];
        foreach ($list as $reduce) {
            // 满额
            if ($reduce['full_type'] == 1) {
                if ($total_price < $reduce['full_value']) {
                    continue;
                }
            } else {
                // 满件数
                if ($total_num < $reduce['full_value']) {
                    continue;
                }
            }
            $key = $reduce['fullreduce_id'];
            // 计算减的金额
            $data[$key] = [
                'fullreduce_id' => $reduce['fullreduce_id'],
                'active_name' => $reduce['active_name'],
            ];
            // 计算满减金额
            if ($reduce['reduce_type'] == 1) {
                //满金额
                $data[$key]['reduced_price'] = $reduce['reduce_value'];
            } else {
                // 折扣比例
                $discountRatio = helper::bcdiv($reduce['reduce_value'], 100);
                $data[$key]['reduced_price'] = max(0.01, helper::bcmul($total_price, $discountRatio));
            }
        }
        if (count($data) == 0) {
            return false;
        } else {
            // 根据折扣金额排序并返回第一个
            $reduce = array_sort($data, 'reduced_price', true);
            $reduce = current($reduce);
            return $reduce;
        }
    }

    /**
     * 列表
     */
    public function getList($shop_supplier_id)
    {
        return $this->where('is_delete', '=', '0')
            ->where('shop_supplier_id', '=', $shop_supplier_id)
            ->order(['reduce_value' => 'desc', 'create_time' => 'asc'])
            ->limit(3)
            ->select();
    }

}