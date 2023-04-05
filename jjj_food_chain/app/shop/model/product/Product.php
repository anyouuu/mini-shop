<?php

namespace app\shop\model\product;

use app\common\model\product\Product as ProductModel;
use app\common\library\helper;

/**
 * 商品模型
 */
class Product extends ProductModel
{
    /**
     * 添加商品
     */
    public function add($data)
    {
        //$data = json_decode($data, 1);
        if (!isset($data['image']) || empty($data['image'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $data['app_id'] = self::$app_id;
        // 开启事务
        $this->startTrans();
        try {
            // 添加商品
            $this->save($data);
            // 商品规格
            $this->addProductSpec($data);
            // 商品图片
            $this->addProductImages($data['image']);
            //更新属性
            (new Attribute)->updateAttr($data['product_attr']);
            //更新加料
            (new Feed)->updateFeed($data['product_feed']);
            //更新单位
            (new Unit)->updateUnit($data['product_unit']);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 添加商品图片
     */
    private function addProductImages($images)
    {
        $this->image()->delete();
        $data = array_map(function ($images) {
            return [
                'image_id' => isset($images['file_id']) ? $images['file_id'] : $images['image_id'],
                'app_id' => self::$app_id
            ];
        }, $images);
        return $this->image()->saveAll($data);
    }

    /**
     * 编辑商品
     */
    public function edit($data)
    {
        if (!isset($data['image']) || empty($data['image'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        $data['spec_type'] = isset($data['spec_type']) ? $data['spec_type'] : $this['spec_type'];
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $productSkuIdList = helper::getArrayColumn(($this['sku']), 'product_sku_id');
        return $this->transaction(function () use ($data, $productSkuIdList) {
            $this->save($data);
            // 商品规格
            $this->addProductSpec($data, $productSkuIdList);
            // 商品图片
            $this->addProductImages($data['image']);
            //更新属性
            (new Attribute)->updateAttr($data['product_attr']);
            //更新加料
            (new Feed)->updateFeed($data['product_feed']);
            //更新单位
            (new Unit)->updateUnit($data['product_unit']);
            return true;
        });
    }

    /**
     * 添加商品规格
     */
    private function addProductSpec($data, $productSkuIdList = [])
    {
        // 更新模式: 先删除所有规格
        $model = new ProductSku;
        $stock = 0;//总库存
        $product_price = 0;//价格
        $cost_price = 0;
        $bag_price = 0;
        // 添加规格数据
        if ($data['spec_type'] == '10') {
            $sku = $data['sku'][0];
            // 单规格
            $sku['app_id'] = self::$app_id;
            $this->sku()->save($sku);
            $stock = $sku['stock_num'];
            $product_price = $sku['product_price'];
            $cost_price = $sku['cost_price'];
            $bag_price = $sku['bag_price'];
        } else if ($data['spec_type'] == '20') {
            //更新规格
            (new Spec)->updateSpec($data['sku']);
            // 添加商品sku
            $model->addSkuList($this['product_id'], $data['sku'], $productSkuIdList);
            $product_price = $data['sku'][0]['product_price'];
            $cost_price = $data['sku'][0]['cost_price'];
            $bag_price = $data['sku'][0]['bag_price'];
            foreach ($data['sku'] as $item) {
                $stock += $item['stock_num'];
                if ($item['product_price'] < $product_price) {
                    $product_price = $item['product_price'];
                }
                if ($item['cost_price'] < $cost_price) {
                    $cost_price = $item['cost_price'];
                }
                if ($item['bag_price'] < $bag_price) {
                    $bag_price = $item['bag_price'];
                }
            }
        }
        $this->save([
            'product_stock' => $stock,
            'product_price' => $product_price,
            'cost_price' => $cost_price,
            'bag_price' => $bag_price
        ]);
    }

    /**
     * 修改商品状态
     */
    public function setStatus($state)
    {
        return $this->save(['product_status' => $state]) !== false;
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }


    /**
     * 获取商品告急数量总数
     */
    public function getProductStockTotal($shop_supplier_id = 0)
    {
        $model = $this;
        if ($shop_supplier_id) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        return $model->where('is_delete', '=', 0)->where('product_stock', '<', 20)->count();
    }

    public function getProductId($search)
    {
        $res = $this->where('product_name', 'like', '%' . $search . '%')->select()->toArray();
        return array_column($res, 'product_id');
    }

    /**
     * 获取数量
     */
    public function getCount($type, $shop_supplier_id,$product_type=0)
    {
        $model = $this;
        //已下架
        if ($type == 'lower') {
            $model = $model->where('product_status', '=', 20);
        }
        if ($shop_supplier_id != 0) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        return $model->where('product_type', '=', $product_type)
            ->where('is_delete', '=', 0)
            ->count();
    }

    /**
     * 查询指定商品
     * @param $value
     */
    public function getProduct($value)
    {
        return $this->with(['image.file'])->where('product_id', 'in', $value)->hidden(['content'])->select();
    }

    /**
     * 同步商品到门店
     * @param $data
     */
    public function transmit($data)
    {
        $product_list = $this->where('product_id', 'in', $data['product_id'])
            ->where('is_delete', '=', 0)
            ->with(['image', 'sku'])
            ->select();
        // 开启事务
        $this->startTrans();
        try {
            foreach ($product_list as $item) {
                foreach ($data['shop_supplier_id'] as $value) {
                    $product = $this->where('product_id', '=', $item['product_id'])
                        ->where('shop_supplier_id', 'in', $value)
                        ->where('is_delete', '=', 0)->find();
                    if ($product) {
                        $product->update($item);
                        $this->addImages($item['image']);
                        (new ProductSku)->where('product_id', '=', $product['product_id'])->delete();
                        $this->addProductSku($data['sku']);
                    } else {
                        unset($item['product_id']);
                        unset($item['create_time']);
                        unset($item['update_time']);
                        $item['shop_supplier_id'] = $value;
                        $this->save($item);
                        $this->addImages($item['image']);
                        $this->addProductSku($data['sku']);
                    }
                }
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 添加商品图片
     */
    private function addImages($images)
    {
        $this->image()->delete();
        $data = array_map(function ($images) {
            return [
                'image_id' => $images['image_id'],
                'app_id' => self::$app_id
            ];
        }, $images);
        return $this->image()->saveAll($data);
    }

    /**
     * 添加商品规格
     */
    private function addProductSku($data, $isUpdate = false, $productSkuIdList = [])
    {
        // 更新模式: 先删除所有规格
        $model = new ProductSku;
        $stock = 0;//总库存
        $product_price = 0;//价格
        $cost_price = 0;
        $bag_price = 0;
        // 添加规格数据
        if ($data['spec_type'] == '10') {
            // 单规格
            $this->sku()->save($data['sku']);
            $stock = $data['sku']['stock_num'];
            $product_price = $data['sku']['product_price'];
            $cost_price = $data['sku']['cost_price'];
            $bag_price = $data['sku']['bag_price'];
        } else if ($data['spec_type'] == '20') {
            //更新规格
            (new Spec)->updateSpec($data['sku']);
            // 添加商品sku
            $model->addSkuList($this['product_id'], $data['sku'], $productSkuIdList);
            $product_price = $data['sku'][0]['product_price'];
            $cost_price = $data['sku'][0]['cost_price'];
            $bag_price = $data['sku'][0]['bag_price'];
            foreach ($data['sku'] as $item) {
                $stock += $item['stock_num'];
                if ($item['product_price'] < $product_price) {
                    $product_price = $item['product_price'];
                }
                if ($item['cost_price'] < $cost_price) {
                    $cost_price = $item['cost_price'];
                }
                if ($item['bag_price'] < $bag_price) {
                    $bag_price = $item['bag_price'];
                }
            }
        }
        $this->save([
            'product_stock' => $stock,
            'product_price' => $product_price,
            'cost_price' => $cost_price,
            'bag_price' => $bag_price
        ]);
    }
}
