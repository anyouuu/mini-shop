<?php

namespace app\api\model\page;
use app\common\model\page\CenterMenu as CenterMenuModel;
use think\facade\Cache;

/**
 * 菜单模型
 */
class CenterMenu extends CenterMenuModel
{

    /**
     * 获取列表
     */
    public static function getMenu($user, $source)
    {
        // 系统菜单
        $sys_menus = CenterMenuModel::getSysMenu();
        // 查询用户菜单
        $model = new CenterMenuModel();
        $user_menus = $model->getAll();
        $user_menu_tags = [];
        foreach ($user_menus as $menu){
            $menu['label'] != '' && array_push($user_menu_tags, $menu['label']);
        }
        $save_data = [];
        foreach($sys_menus as $menu){
            if($menu['label'] != '' && !in_array($menu['label'], $user_menu_tags)){
                $save_data[] = array_merge($sys_menus[$menu['label']], [
                    'sort' => 100,
                    'app_id' => self::$app_id
                ]);
            }
        }
        if(count($save_data) > 0){
            $model->saveAll($save_data);
            Cache::delete('center_menu_' . self::$app_id);
            $user_menus = $model->getAll();
        }
        $menus_arr = [];
        foreach ($user_menus as $menu) {
            if($menu['label'] != '' && $menu['status'] == 1){
                array_push($menus_arr, $menu);
            }
        }
        foreach ($menus_arr as $menus){
            if(strpos($menus['image_url'], 'http') !== 0){
                $menus['image_url'] = self::$base_url . $menus['image_url'];
            }
        }
        return $menus_arr;
    }
}