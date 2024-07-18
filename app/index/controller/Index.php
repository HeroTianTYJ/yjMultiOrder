<?php

namespace app\index\controller;

use app\index\library\Tool;
use app\index\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;
use yjrj\Wechat;

class Index extends Base
{
    public function index($module = '')
    {
        $Lists = new model\Lists();
        $listsOne = [];
        if ($module) {
            $listsOne = $Lists->one2($module);
            if (!$listsOne) {
                return $this->failed('不存在此列表页！');
            }
        } elseif (Request::param('id')) {
            $listsOne = $Lists->one();
            if (!$listsOne) {
                return $this->failed('不存在此列表页！');
            }
        } else {
            if (Config::get('system.home_page') == 0) {
                return $this->failed('暂无首页，管理员可通过后台 - 系统设置 - 前台设置指定首页！');
            } elseif (Config::get('system.home_page') == 1) {
                $listsOne = $Lists->one(Config::get('system.lists_id'));
                if (!$listsOne) {
                    return $this->failed('后台 - 系统设置 - 前台设置指定的首页已被删除，或被设置为了前台不显示，请重新设置！');
                }
            } elseif (Config::get('system.home_page') == 2) {
                return (new Item())->index(Config::get('system.item_id'));
            } elseif (Config::get('system.home_page') == 3) {
                return (new Category())->index();
            }
        }

        Tool::click($listsOne['id'], 'lists');

        if (
            Config::get('system.wechat_app_id') && Config::get('system.wechat_app_secret') &&
            in_array(device(), ['androidWechat', 'iphoneWechat', 'windowsWechat', 'macWechat'])
        ) {
            $Wechat = new Wechat([
                'app_id' => Config::get('system.wechat_app_id'),
                'app_secret' => Config::get('system.wechat_app_secret')
            ]);
            $listsOne['share'] = $Wechat->getShareConfig();
        } else {
            $listsOne['share'] = false;
        }

        $Text = new model\Text();
        $listsOne['code'] = $Text->content($listsOne['text_id_code']);
        $listsOne['item_ids'] = $Text->content($listsOne['text_id_item_ids']);
        $listsOne['banner'] = $Text->content($listsOne['text_id_banner']);
        $listsOne['nav'] = $Text->content($listsOne['text_id_nav']);

        $Item = new model\Item();
        $itemAll = $listsOne['page'] ?
            $Item->all(
                $listsOne['item_ids'],
                $this->page(
                    $Item->totalCount($listsOne['item_ids']),
                    $listsOne['page'],
                    explode('?', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' .
                        $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])[0]
                ),
                $listsOne['page']
            ) : $Item->all($listsOne['item_ids']);
        if ($itemAll) {
            foreach ($itemAll as $key => $value) {
                if ($value['tag']) {
                    $itemAll[$key]['tag'] = explode("\r\n", $value['tag'])[0];
                }
            }
        }
        $listsOne['item'] = $itemAll;

        $bannerStr = '';
        if ($listsOne['banner']) {
            $banner = explode("\r\n", $listsOne['banner']);
            $bottom = 15;
            $bannerStr = '<div class="banner"><ul class="swiper-wrapper">';
            foreach ($banner as $value) {
                $temp = explode('|', $value);
                if (count($temp) == 1) {
                    $bannerStr .= '<li class="swiper-slide"><img src="' . (Config::get('system.qiniu_domain') ?
                        Config::get('system.qiniu_domain') : Config::get('dir.upload')) .
                        str_replace(['[img=', ']'], '', $temp[0]) . '?' . staticCache() . '" alt="轮播图"></li>';
                } elseif (count($temp) == 2) {
                    $bannerStr .= '<li class="swiper-slide"><a href="' . $temp[1] . '" target="_blank"><img src="' .
                        (Config::get('system.qiniu_domain') ? Config::get('system.qiniu_domain') :
                            Config::get('dir.upload')) . str_replace(['[img=', ']'], '', $temp[0]) . '?' .
                        staticCache() . '" alt="轮播图"></a></li>';
                } elseif (count($temp) == 3) {
                    $bannerStr .= '<li class="swiper-slide"><a href="' . $temp[1] . '" target="_blank"><img src="' .
                        (Config::get('system.qiniu_domain') ? Config::get('system.qiniu_domain') :
                            Config::get('dir.upload')) . str_replace(['[img=', ']'], '', $temp[0]) . '?' .
                        staticCache() . '" alt="轮播图"></a><span>' . $temp[2] . '</span></li>';
                    $bottom = 36;
                }
            }
            $bannerStr .= '</ul><span class="arrow prev">&lt;</span><span class="arrow next">&gt;</span>' .
                '<div class="pagination" style="bottom:' . $bottom . 'px;"></div></div>';
        }
        $listsOne['banner_str'] = $bannerStr;

        $navStr = '';
        if ($listsOne['nav']) {
            $nav = explode("\r\n", $listsOne['nav']);
            $icon = explode("\r\n", $listsOne['icon']);
            $navStr = '<nav class="nav"><ul>';
            foreach ($nav as $key => $value) {
                if (isset($icon[$key]) && strstr($icon[$key], '[img=')) {
                    preg_match('/\[img=(.*)]/U', $icon[$key], $img);
                    $value = str_replace(
                        '<strong>',
                        '<strong><span style="background:url(' . (Config::get('system.qiniu_domain') ?
                            Config::get('system.qiniu_domain') :
                            Config::get('url.web1') . Config::get('dir.upload')) . $img[1] . ') no-repeat;"></span>',
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
        $listsOne['nav_str'] = $navStr;

        View::assign(['One' => $listsOne]);
        return $this->view();
    }
}
