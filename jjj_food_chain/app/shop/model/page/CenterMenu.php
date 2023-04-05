<?php

namespace app\shop\model\page;
use app\common\model\page\CenterMenu as CenterMenuModel;
/**
 * 模型
 */
class CenterMenu extends CenterMenuModel
{

    /**
     * 添加新记录
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {   
        unset($data['create_time']);unset($data['update_time']);
        $data['update_time'] = time();
        return $this->withoutGlobalScope()->save($data);
    }

    /**
     * 删除记录
     */
    public function remove()
    {
        return $this->delete();
    }
}