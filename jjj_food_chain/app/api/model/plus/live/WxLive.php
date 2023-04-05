<?php

namespace app\api\model\plus\live;

use app\common\model\plus\live\WxLive as WxLiveModel;

/**
 * 微信直播模型
 */
class WxLive extends WxLiveModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'is_delete',
        'app_id',
    ];

    /**
     * 获取直播列表
     */
    public function getList($params)
    {
        return $this->order([
            'is_top' => 'desc',
            'live_status' => 'asc',
            'create_time' => 'desc'
        ])->paginate($params);
    }
}