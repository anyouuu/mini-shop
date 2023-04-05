<?php

namespace app\common\model\product;

use think\facade\Cache;
use app\common\model\BaseModel;

/**
 * 产品分类模型
 */
class Category extends BaseModel
{
    protected $pk = 'category_id';
    protected $name = 'category';

    /**
     * 分类图片
     */
    public function images()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }

    /**
     * 充值套餐详情
     */
    public static function detail($category_id)
    {
        return self::find($category_id);
    }


    public function detailWithImage($where)
    {
        return $this->with(['image'])->where($where)->find();
    }

    /**
     * 所有分类
     */
    public static function getALL($type, $is_special = 0)
    {
        $model = new static;
        if (!Cache::get('category_' . $model::$app_id . $type . $is_special)) {
            $data = $model->with(['images'])->where('type', '=', $type)->where('is_special', '=', $is_special)->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
            $all = !empty($data) ? $data->toArray() : [];
            $tree = [];
            foreach ($all as $first) {
                if ($first['parent_id'] != 0) continue;
                $twoTree = [];
                foreach ($all as $two) {
                    if ($two['parent_id'] != $first['category_id']) continue;
                    $threeTree = [];
                    foreach ($all as $three)
                        $three['parent_id'] == $two['category_id']
                        && $threeTree[$three['category_id']] = $three;
                    !empty($threeTree) && $two['child'] = $threeTree;
                    array_push($twoTree, $two);
                }
                if (!empty($twoTree)) {
                    $temp_two_tree = array_column($twoTree, 'sort');
                    array_multisort($temp_two_tree, SORT_ASC, $twoTree);
                    $first['child'] = $twoTree;
                }
                array_push($tree, $first);
            }
            Cache::tag('cache')->set('category_' . $model::$app_id . $type . $is_special, compact('all', 'tree'));
        }
        return Cache::get('category_' . $model::$app_id . $type . $is_special);
    }

    /**
     * 获取所有分类
     */
    public static function getCacheAll($type = 0, $is_special = 0)
    {
        return self::getALL($type, $is_special)['all'];
    }

    /**
     * 获取所有分类(树状结构)
     */
    public static function getCacheTree($type = 0, $is_special = 0)
    {
        return self::getALL($type, $is_special)['tree'];
    }

    /**
     * 获取所有分类(树状结构)
     * @return string
     */
    public static function getCacheTreeJson()
    {
        return json_encode(static::getCacheTree());
    }

    /**
     * 获取指定分类下的所有子分类id
     */
    public static function getSubCategoryId($parent_id, $all = [])
    {
        $arrIds = [$parent_id];
        empty($all) && $all = self::getCacheAll();
        foreach ($all as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                unset($all[$key]);
                $subIds = self::getSubCategoryId($item['category_id'], $all);
                !empty($subIds) && $arrIds = array_merge($arrIds, $subIds);
            }
        }
        return $arrIds;
    }

    /**
     * 指定的分类下是否存在子分类
     */
    protected static function hasSubCategory($parentId)
    {
        $all = self::getCacheAll();
        foreach ($all as $item) {
            if ($item['parent_id'] == $parentId) {
                return true;
            }
        }
        return false;
    }


    /**
     * 关联图片
     */
    public function image()
    {
        return $this->belongsTo('app\common\model\file\UploadFile', 'image_id', 'file_id');
    }

    /**
     * 获取所有一级分类
     */
    public static function getFirstCategory()
    {
        return self::where('parent_id', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }

    //新增特殊分类
    public function addSpecial($app_id)
    {
        $data = [
            ['name' => '新品', 'is_special' => 1, 'type' => 0, 'app_id' => $app_id],
            ['name' => '热卖', 'is_special' => 1, 'type' => 0, 'app_id' => $app_id],
            ['name' => '套餐', 'is_special' => 1, 'type' => 0, 'app_id' => $app_id],
            ['name' => '新品', 'is_special' => 1, 'type' => 1, 'app_id' => $app_id],
            ['name' => '热卖', 'is_special' => 1, 'type' => 1, 'app_id' => $app_id],
            ['name' => '套餐', 'is_special' => 1, 'type' => 1, 'app_id' => $app_id],
        ];
        $this->saveAll($data);
    }
}
