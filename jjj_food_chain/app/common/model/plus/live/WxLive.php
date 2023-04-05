<?php

namespace app\common\model\plus\live;

use app\common\model\BaseModel;

/**
 * 微信直播模型
 */
class WxLive extends BaseModel
{
    protected $name = 'app_wx_live';
    protected $pk = 'live_id';
    //附加字段
    protected $append = ['start_time_text','end_time_text'];

    /**
     * 有效期-开始时间
     */
    public function getStartTimeTextAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['start_time']);
    }

    /**
     * 有效期-开始时间
     * @param $value
     * @return array
     */
    public function getEndTimeTextAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['end_time']);
    }

    /**
     * 详情
     */
    public static function detail($live_id)
    {
        return (new static())->find($live_id);
    }

}
