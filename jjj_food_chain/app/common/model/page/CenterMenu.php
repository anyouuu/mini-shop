<?php

namespace app\common\model\page;

use think\facade\Cache;
use think\facade\Request;
use app\common\model\BaseModel;

/**
 * 个人中心菜单模型
 */
class CenterMenu extends BaseModel
{
    protected $name = 'center_menu';
    protected $pk = 'menu_id';

    /**
     * 获取列表
     */
    public function getList($limit = 20)
    {   
        return $this->withoutGlobalScope()->order(['sort' => 'asc'])
            ->paginate($limit, false, [
                'query' => Request::instance()->request()
            ]);
    }

    /**
     * 详情
     */
    public static function detail($menu_id)
    {
        return self::withoutGlobalScope()->find($menu_id);
    }

    /**
     * 查询所有
     */
    public static function getAll(){
        $model = new static();
        if (!Cache::get('center_menu_' . $model::$app_id)) {
            $list = $model->order(['sort' => 'asc'])->select();
            if(count($list) == 0){
                $sys_menus = $model->getSysMenu();
                $save_data = [];
                foreach($sys_menus as $menu){
                    $save_data[] = array_merge($sys_menus[$menu['label']], [
                        'sort' => 100,
                        'app_id' => self::$app_id
                    ]);
                }
                $model->saveAll($save_data);
                $list = $model->order(['sort' => 'asc'])->select();
            }
            Cache::tag('cache')->set('center_menu_' . $model::$app_id, $list);
        }
        return Cache::get('center_menu_' . $model::$app_id);
    }


    /**
     * 系统菜单
     */
    public static function getSysMenu(){
        return [
            'address' => [
                'label' => 'address',
                'title' => '收货地址',
                'link_url' => '/pages/user/address/address',
                'image_url' => 'image/menu/address.png'
            ],
            'setting' => [
                'label' => 'setting',
                'title' => '设置',
                'link_url' => '/pages/user/set/set',
                'image_url' => 'image/menu/setting.png'
            ],
        ];
    }
}