<?php

namespace app\shop\controller\link;

use app\shop\controller\Controller;
use app\shop\model\plus\assemble\Active;
use app\shop\model\plus\seckill\Active as ActiveModel;
use app\shop\model\plus\bargain\Bargain;
use app\shop\model\plus\giftpackage\GiftPackage;
use app\shop\model\page\Page as PageModel;

/**
 * Class Link
 * @package app\shop\controller\link
 * 超链接控制器
 */
class Link extends Controller
{
    /**
     *获取数据
     */
    public function index($activeName)
    {
        if ($activeName == 'market') {
            //拼团
            $model = new Active();
            $data = $model->getDatas($this->postData());
            foreach ($data as $k => $v) {
                $data[$k]['type'] = '营销';
                $data[$k]['url'] = 'pages/plus/group/list/list';
            }
        } else if ($activeName == 'second') {
            $model = new ActiveModel();
            $data = $model->getDatas($this->postData());
            foreach ($data as $k => $v) {
                $data[$k]['type'] = '营销';
                $data[$k]['url'] = 'pages/plus/sharpproduct/list/list';
            }
        } else if ($activeName == 'third') {
            $model = new Bargain();
            $data = $model->getDatas($this->postData());
            foreach ($data as $k => $v) {
                $data[$k]['title'] = $v['name'];
                $data[$k]['type'] = '营销';
                $data[$k]['url'] = 'pages/plus/bargaining/list/list';
            }
        } else if ($activeName == 'fourth') {
            $model = new GiftPackage();
            $date['search'] = '';
            $data = $model->getDatas($date);
            foreach ($data as $k => $v) {
                $data[$k]['title'] = $v['name'];
                $data[$k]['type'] = '营销';
                $data[$k]['url'] = 'pages/plus/giftpackage/giftpackage&id=' . $v['gift_package_id'];
            }
        }
        return $this->renderSuccess('', compact('data'));
    }

    /**
     * 获取自定义页面
     */
    public function getPageList()
    {
        $model = new PageModel;
        $list = $model->getLists();
        return $this->renderSuccess('', compact('list'));
    }
}
