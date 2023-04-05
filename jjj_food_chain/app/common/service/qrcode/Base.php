<?php

namespace app\common\service\qrcode;

use app\common\library\easywechat\AppWx;
use Endroid\QrCode\QrCode;

/**
 * 二维码服务基类
 */
class Base
{
    /**
     * 构造方法
     */
    public function __construct()
    {
    }

    /**
     * 保存小程序码到文件
     */
    protected function saveQrcode($app_id, $scene, $page)
    {
        // 文件目录
        $dirPath = root_path('public') . "/temp/{$app_id}/image_wx";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene . $page) . '.png';
        // 文件路径
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $savePath;
        // 小程序配置信息
        $app = AppWx::getApp($app_id);
        // 请求api获取小程序码
        $response = $app->app_code->getUnlimit($scene, [
            'page' => $page,
            'width' => 430
        ]);
        // 保存小程序码到文件
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $response->saveAs($dirPath, $fileName);
        }
        return $savePath;
    }

    /**
     * 保存普通二维码到文件
     */
    protected function saveMpQrcode(\Endroid\QrCode\QrCode $qrcode, $app_id, $scene, $source)
    {
        // 文件目录
        $dirPath = root_path('public') . "/temp/{$app_id}/{$source}";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene) . '.png';
        // 文件路径
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $savePath;
        // 保存二维码到文件
        $qrcode->writeFile($savePath);
        return $savePath;
    }

    /**
     * 获取网络图片到临时目录
     */
    protected function saveTempImage($app_id, $url, $mark = 'temp')
    {
        $dirPath = root_path('public') . "temp/{$app_id}/{$mark}";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        $savePath = $dirPath . '/' . $mark . '_' . md5($url) . '.png';
        if (file_exists($savePath)) return $savePath;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $img = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($savePath, 'w');
        fwrite($fp, $img);
        fclose($fp);
        return $savePath;
    }

    /**
     * 保存小程序码到文件
     */
    protected function saveQrcodeToDir($app_id, $page, $savePath, $table_id, $shop_supplier_id)
    {
        // 小程序配置信息
        $app = AppWx::getApp($app_id);
        // 小程序码参数
        $scene = "table_id:{$table_id}&{$shop_supplier_id}";
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene . $page) . '.png';
        // 请求api获取小程序码
        $response = $app->app_code->getUnlimit($scene, [
            'page' => $page,
            'width' => 430
        ]);
        // 保存小程序码到文件
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $response->saveAs($savePath, $fileName);
        }
        return true;
    }

    /**
     * 保存二维码码到文件
     */
    protected function saveMpQrcodeToDir($page, $savePath, $table_id, $app_id, $shop_supplier_id)
    {
        $qrcode = new QrCode(base_url() . $page . '?table_id=' . $table_id . '&app_id=' . $app_id . '&shop_supplier_id' . $shop_supplier_id);
        $scene = "shop_supplier_id:{$shop_supplier_id},table_id:{$table_id}";
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene) . '.png';
        // 保存二维码到文件
        $path = "{$savePath}{$fileName}";
        $qrcode->writeFile($path);
        return true;
    }

    /**
     * 保存小程序码到文件
     */
    protected function saveSupplierQrcodeToDir($app_id, $page, $savePath, $shop_supplier_id)
    {
        // 小程序配置信息
        $app = AppWx::getApp($app_id);
        // 小程序码参数
        $scene = "shop_supplier_id:{$shop_supplier_id}";
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene . $page) . '.png';
        // 请求api获取小程序码
        $response = $app->app_code->getUnlimit($scene, [
            'page' => $page,
            'width' => 430
        ]);
        // 保存小程序码到文件
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $response->saveAs($savePath, $fileName);
        }
        return true;
    }

    /**
     * 保存二维码码到文件
     */
    protected function saveSupplierMpQrcodeToDir($page, $savePath, $shop_supplier_id, $app_id)
    {
        $qrcode = new QrCode(base_url() . $page . '?app_id=' . $app_id . '&shop_supplier_id' . $shop_supplier_id);
        $scene = "shop_supplier_id:{$shop_supplier_id}";
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene) . '.png';
        // 保存二维码到文件
        $path = "{$savePath}{$fileName}";
        $qrcode->writeFile($path);
        return true;
    }

    /**
     * 保存邀请好友小程序码到文件
     */
    protected function saveInvitQrcodeToDir($app_id, $page, $savePath, $id)
    {
        // 小程序配置信息
        $app = AppWx::getApp($app_id);
        $scene = "invitid:$id";
        // 请求api获取小程序码
        $response = $app->app_code->getUnlimit($scene, [
            'page' => $page,
            'width' => 430
        ]);
        // 保存小程序码到文件
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $response->saveAs($savePath, $id . '.png');
        }

        return true;
    }

    /**
     * 保存邀请好友小程序码到文件
     */
    protected function saveInvitMpQrcodeToDir($page, $savePath, $id, $app_id)
    {
        $qrcode = new QrCode(base_url() . '/' . $page . '?invitation_id=' . $id . '&app_id=' . $app_id);
        // 保存二维码到文件
        $path = "{$savePath}{$id}" . '.png';
        $qrcode->writeFile($path);
        return true;
    }
}