<?php

namespace app\api\controller;

use app\api\model\page\Page as AppPage;
use app\api\model\settings\Setting as SettingModel;
use app\common\model\app\AppUpdate as AppUpdateModel;
use app\api\model\supplier\Supplier as SupplierModel;
use app\common\library\easywechat\AppMp;

/**
 * 页面控制器
 */
class Index extends Controller
{
    /**
     * 首页
     */
    public function index($page_id = null, $url = '')
    {
        // 页面元素
        $data = AppPage::getPageData($this->getUser(false), $page_id);
        // 页面元素
        $user = $this->getUser(false);
        //获取默认门店id
        $supplier = (new SupplierModel)->getDefault($this->postData());
        $signPackage = '';
        if ($url != '') {
            $app = AppMp::getApp($this->app_id);
            $app->jssdk->setUrl($url);
            $signPackage = $app->jssdk->buildConfig(array('getLocation', 'openLocation'), false);
        }
        return $this->renderSuccess('', compact('data','user', 'supplier', 'signPackage'));
    }

    //底部导航
    public function nav()
    {
        $data['nav'] = SettingModel::getItem('nav');
        $data['bottomnav'] = SettingModel::getItem('bottomnav');
        $data['theme'] = SettingModel::getItem('theme');
        return $this->renderSuccess('', $data);
    }

    // app更新
    public function update($name, $version, $platform)
    {
        $result = [
            'update' => false,
            'wgtUrl' => '',
            'pkgUrl' => '',
        ];
        try {
            $model = AppUpdateModel::getLast();
            // 这里简单判定下，不相等就是有更新。
            if ($model && $version != $model['version']) {
                $currentVersions = explode('.', $version);
                $resultVersions = explode('.', $model['version']);

                if ($currentVersions[0] < $resultVersions[0]) {
                    // 说明有大版本更新
                    $result['update'] = true;
                    $result['pkgUrl'] = $platform == 'android' ? $model['pkg_url_android'] : $model['pkg_url_ios'];
                } else {
                    // 其它情况均认为是小版本更新
                    $result['update'] = true;
                    $result['wgtUrl'] = $model['wgt_url'];
                }
            }
        } catch (\Exception $e) {

        }
        return $this->renderSuccess('', compact('result'));
    }
}
