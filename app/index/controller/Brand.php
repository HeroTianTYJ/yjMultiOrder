<?php

namespace app\index\controller;

use app\index\library\Tool;
use app\index\model;
use think\facade\Config;
use think\facade\Route;
use think\facade\View;
use yjrj\Wechat;

class Brand extends Base
{
    public function index()
    {
        $Brand = new model\Brand();
        $brandOne = $Brand->one();
        if (!$brandOne) {
            return $this->failed('不存在此品牌！');
        }

        Tool::click($brandOne['id'], 'brand');

        $brandPageOne = (new model\BrandPage())->one();

        if (
            Config::get('system.wechat_app_id') && Config::get('system.wechat_app_secret') &&
            in_array(device(), ['androidWechat', 'iphoneWechat', 'windowsWechat', 'macWechat'])
        ) {
            $Wechat = new Wechat([
                'app_id' => Config::get('system.wechat_app_id'),
                'app_secret' => Config::get('system.wechat_app_secret')
            ]);
            $brandPageOne['share'] = $Wechat->getShareConfig();
            $brandPageOne['share_pic'] = $brandPageOne['share_pic'] ?: $brandOne['logo'];
        } else {
            $brandPageOne['share'] = false;
        }

        $Text = new model\Text();
        $brandPageOne['code'] = $Text->content($brandPageOne['text_id_code']);
        $brandPageOne['nav'] = $Text->content($brandPageOne['text_id_nav']);

        $navStr = '';
        if ($brandPageOne['nav']) {
            $nav = explode("\r\n", $brandPageOne['nav']);
            $icon = explode("\r\n", $brandPageOne['icon']);
            $navStr = '<nav class="nav"><ul>';
            foreach ($nav as $key => $value) {
                if (isset($icon[$key]) && strstr($icon[$key], '[img=')) {
                    preg_match('/\[img=(.*)]/U', $icon[$key], $img);
                    $value = str_replace(
                        '<strong>',
                        '<strong><span style="background:url(' . (Config::get('system.qiniu_domain') ?
                            Config::get('system.qiniu_domain') :
                            Config::get('url.web1') . Config::get('dir.upload')) . $img[1] .
                        ') no-repeat;"></span>',
                        $value
                    );
                } else {
                    $value = str_replace('<strong>', '<strong><span class="' . ($icon[$key] ?? '') .
                        '"></span>', $value);
                }
                $navStr .= '<li>' . $value . '</li>';
            }
            $navStr .= '</ul></nav>';
        }
        $brandPageOne['nav_str'] = $navStr;

        $Category = new model\Category();
        $categoryOne = $Category->one($brandOne['category_id'], 0);
        $categoryOne2 = $Category->one($categoryOne['parent_id'], 0);
        foreach (
            [
                'name', 'title', 'keyword', 'description', 'copyright', 'code', 'nav_str', 'share_title', 'share_desc'
            ] as $value
        ) {
            $brandPageOne[$value] = str_replace(
                ['{BrandName}', '{CategoryName}', '{ParentCategoryName}'],
                [
                    $brandOne['name'],
                    $categoryOne ? $categoryOne['name'] : '',
                    $categoryOne2 ? $categoryOne2['name'] : ''
                ],
                $brandPageOne[$value]
            );
        }

        $brandPageOne['brand'] = $Brand->all($brandOne['category_id']);

        $Item = new model\Item();
        $itemAll = $brandPageOne['page'] ?
            $Item->all2(
                $brandOne['id'],
                $this->page(
                    $Item->totalCount2($brandOne['id']),
                    $brandPageOne['page'],
                    Route::buildUrl('/brand/index', ['id' => $brandOne['id']])
                ),
                $brandPageOne['page']
            ) : $Item->all2($brandOne['id']);
        foreach ($itemAll as $key => $value) {
            if ($value['tag']) {
                $itemAll[$key]['tag'] = explode("\r\n", $value['tag'])[0];
            }
        }
        $brandPageOne['item'] = $itemAll;

        View::assign(['One' => $brandPageOne]);
        return $this->view();
    }
}
