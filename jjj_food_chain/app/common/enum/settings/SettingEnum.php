<?php

namespace app\common\enum\settings;

use MyCLabs\Enum\Enum;

/**
 * 商城设置枚举类
 */
class SettingEnum extends Enum
{
    // 商城设置
    const STORE = 'store';
    // 交易设置
    const TRADE = 'trade';
    // 短信通知
    const SMS = 'sms';
    // 模板消息
    const TPL_MSG = 'tplMsg';
    // 上传设置
    const STORAGE = 'storage';
    // 小票打印
    const PRINTER = 'printer';
    // 商品推荐
    const RECOMMEND = 'recommend';
    // 获取手机号
    const GETPHOME = 'getPhone';
     // 系统配置
    const SYS_CONFIG = 'sys_config';
    // 主体设置
    const THEME = 'theme';
    // 配送设置
    const DELIVER = 'deliver';
    /**
     * 获取订单类型值
     */
    public static function data()
    {
        return [
            self::STORE => [
                'value' => self::STORE,
                'describe' => '商城设置',
            ],
            self::TRADE => [
                'value' => self::TRADE,
                'describe' => '交易设置',
            ],
            self::SMS => [
                'value' => self::SMS,
                'describe' => '短信通知',
            ],
            self::TPL_MSG => [
                'value' => self::TPL_MSG,
                'describe' => '模板消息',
            ],
            self::STORAGE => [
                'value' => self::STORAGE,
                'describe' => '上传设置',
            ],
            self::PRINTER => [
                'value' => self::PRINTER,
                'describe' => '小票打印',
            ],
            self::RECOMMEND => [
                'value' => self::RECOMMEND,
                'describe' => '商品推荐',
            ],
            self::GETPHOME => [
                'value' => self::GETPHOME,
                'describe' => '获取手机号',
            ],
            self::SYS_CONFIG => [
                'value' => self::SYS_CONFIG,
                'describe' => '系统设置',
            ],
            self::THEME => [
                'value' => self::THEME,
                'describe' => '主题设置',
            ],
            self::DELIVER => [
                'value' => self::DELIVER,
                'describe' => '配送设置',
            ],
        ];
    }

}