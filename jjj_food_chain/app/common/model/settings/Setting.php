<?php

namespace app\common\model\settings;

use app\common\enum\settings\SettingEnum;
use think\facade\Cache;
use app\common\model\BaseModel;

/**
 * 系统设置模型
 */
class Setting extends BaseModel
{
    protected $name = 'setting';
    protected $createTime = false;

    /**
     * 获取器: 转义数组格式
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 修改器: 转义成json格式
     */
    public function setValuesAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 获取指定项设置
     */
    public static function getItem($key, $app_id = null)
    {
        $data = self::getAll($app_id);
        $data_key = $data[$key];
        if (isset($data_key)) {
            $data_key = $data[$key]['values'];
            jsonRecursive($data_key);
        } else {
            $data_key = [];
        }
        return $data_key;
    }

    /**
     * 获取系统配置
     */
    public static function getSysConfig()
    {
        $model = new static;
        $result = $model->withoutGlobalScope()->where('key', '=', SettingEnum::SYS_CONFIG)->value('values');
        if (!$result) {
            $result = $model->defaultData()[SettingEnum::SYS_CONFIG]['values'];
        } else {
            $result = json_decode($result, true);
        }
        return $result;
    }

    /**
     * 获取指定项设置
     */
    public static function getSupplierItem($key, $shop_supplier_id, $app_id = null)
    {
        $data = self::getAll($app_id, $shop_supplier_id);
        $data_key = $data[$key];
        if (isset($data_key)) {
            $data_key = $data[$key]['values'];
            jsonRecursive($data_key);
        } else {
            $data_key = [];
        }

        return $data_key;
    }

    /**
     * 获取设置项信息
     */
    public static function detail($key, $shop_supplier_id = 0)
    {
        $where = [];
        if ($shop_supplier_id) {
            $where['shop_supplier_id'] = $shop_supplier_id;
        }

        return self::where('key', '=', $key)->where($where)->find();
    }

    /**
     * 全局缓存: 系统设置
     */
    public static function getAll($app_id = null, $shop_supplier_id = 0)
    {
        $static = new static;
        is_null($app_id) && $app_id = $static::$app_id;
        if (!$data = Cache::get('setting_' . $app_id . '_' . $shop_supplier_id)) {
            $setting = $static->where(compact('app_id'))->where('shop_supplier_id', $shop_supplier_id)->select();
            $data = empty($setting) ? [] : array_column($static->collection($setting)->toArray(), null, 'key');
            Cache::tag('cache')->set('setting_' . $app_id . '_' . $shop_supplier_id, $data);
        }
        return $static->getMergeData($data);
    }

    /**
     * 数组转换为数据集对象
     */
    public function collection($resultSet)
    {
        $item = current($resultSet);
        if ($item instanceof Model) {
            return \think\model\Collection::make($resultSet);
        } else {
            return \think\Collection::make($resultSet);
        }
    }


    /**
     * 合并用户设置与默认数据
     */
    private function getMergeData($userData)
    {
        $defaultData = $this->defaultData();
        // 商城设置：配送方式
        if (isset($userData['store']['values']['delivery_type'])) {
            unset($defaultData['store']['values']['delivery_type']);
        }
        return array_merge_multiple($defaultData, $userData);
    }

    /**
     * 默认配置
     */
    public function defaultData($storeName = null)
    {
        $url = base_url();
        return [
            'store' => [
                'key' => 'store',
                'describe' => '商城设置',
                'values' => [
                    // 商城名称
                    'name' => $storeName ?: '玖玖珈商城',
                    // 是否记录日志
                    'is_get_log' => true
                ],
            ],
            'trade' => [
                'key' => 'trade',
                'describe' => '交易设置',
                'values' => [
                    'order' => [
                        'close_days' => '3',
                        'receive_days' => '10',
                        'refund_days' => '7'
                    ],
                    'freight_rule' => '10',
                ]
            ],
            'storage' => [
                'key' => 'storage',
                'describe' => '上传设置',
                'values' => [
                    'default' => 'local',
                    'engine' => [
                        'local' => [],
                        'qiniu' => [
                            'bucket' => '',
                            'access_key' => '',
                            'secret_key' => '',
                            'domain' => 'http://'
                        ],
                        'aliyun' => [
                            'bucket' => '',
                            'access_key_id' => '',
                            'access_key_secret' => '',
                            'domain' => 'http://'
                        ],
                        'qcloud' => [
                            'bucket' => '',
                            'region' => '',
                            'secret_id' => '',
                            'secret_key' => '',
                            'domain' => 'http://'
                        ],
                    ]
                ],
            ],
            'sms' => [
                'key' => 'sms',
                'describe' => '短信通知',
                'values' => [
                    'default' => 'aliyun',
                    'engine' => [
                        'aliyun' => [
                            'AccessKeyId' => '',
                            'AccessKeySecret' => '',
                            'sign' => '三勾点单',
                            'login_template' => '',
                            'apply_template' => '',
                            'supplier_reject_code' => '',
                            'supplier_pass_code' => ''
                        ],
                    ],
                ],
            ],
            'tplMsg' => [
                'key' => 'tplMsg',
                'describe' => '模板消息',
                'values' => [
                    'payment' => [
                        'is_enable' => '0',
                        'template_id' => '',
                    ],
                    'delivery' => [
                        'is_enable' => '0',
                        'template_id' => '',
                    ],
                    'refund' => [
                        'is_enable' => '0',
                        'template_id' => '',
                    ],
                ],
            ],
            'printer' => [
                'key' => 'printer',
                'describe' => '小票打印机设置',
                'values' => [
                    'room_open' => '0',   // 是否开启打印
                    'room_printer_id' => '', // 打印机id
                    'buyer_open' => '0',   // 顾客是否开启打印
                    'buyer_printer_id' => '', // 顾客打印机id
                    'seller_open' => '0',   // 商家是否开启打印
                    'seller_printer_id' => '', // 商家打印机id
                    'order_status' => [], // 订单类型 10下单打印 20付款打印 30确认收货打印
                ],
            ],
            'recommend' => [
                'key' => 'recommend',
                'describe' => '商品推荐',
                'values' => [
                    'is_recommend' => '0',
                    'location' => [],
                    'choice' => '0',
                    'type' => '10',
                    'num' => '20',
                    'product' => []
                ],
            ],
            'getPhone' => [
                'key' => 'getPhone',
                'describe' => '获取手机号设置',
                'values' => [
                    // 显示区域
                    'area_type' => [],
                    // 不再提示天数
                    'send_day' => 7
                ],
            ],
            'sys_config' => [
                'key' => 'sys_config',
                'describe' => '系统设置',
                'values' => [
                    'shop_name' => '三勾点单管理系统',
                    'shop_bg_img' => ''
                ]
            ],
            'bottomnav' => [
                'key' => 'bottomnav',
                'describe' => '底部菜单',
                'values' => [
                    // 菜单
                    'menu_type' => 1,//优惠券
                ],
            ],
            'theme' => [
                'key' => 'theme',
                'describe' => '主题设置',
                'values' => [
                    'theme' => '',//主题设置
                ],
            ],
            'deliver' => [
                'key' => 'deliver',
                'describe' => '配送设置',
                'values' => [
                    'default' => 'local',
                    'engine' => [
                        'local' => [
                            'name' => '商家配送',
                            'time'=>0,
                        ],
                    ]
                ],
            ],
        ];
    }

}
