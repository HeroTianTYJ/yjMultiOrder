<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class Lists extends Base
{
    private array $codeType = ['使用全局代码 + 独立代码', '仅使用独立代码'];
    private array $itemType = ['全部', '手选'];

    public function index()
    {
        $listsAll = (new model\Lists())->all();
        if (Request::isAjax()) {
            foreach ($listsAll as $key => $value) {
                $listsAll[$key] = $this->listItem($value);
            }
            return $listsAll->items() ? json_encode($listsAll->items()) : '';
        }
        View::assign(['Total' => $listsAll->total()]);
        Html::wxxcx(Request::get('wxxcx_id'));
        Html::manager(Request::get('manager_id'), 3);
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $listsAdd = (new model\Lists())->add();
                if (is_numeric($listsAdd)) {
                    return $listsAdd > 0 ? showTip('列表页添加成功！') : showTip('列表页添加失败！', 0);
                } else {
                    return showTip($listsAdd, 0);
                }
            }
            Html::codeType($this->codeType);
            Html::itemType($this->itemType);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Lists = new model\Lists();
            $listsOne = $Lists->one();
            if (!$listsOne) {
                return showTip('不存在此列表页！', 0);
            }
            if (Request::get('action') == 'do') {
                $listsModify = $Lists->modify(
                    $listsOne['text_id_code'],
                    $listsOne['text_id_item_ids'],
                    $listsOne['text_id_banner'],
                    $listsOne['text_id_nav']
                );
                return is_numeric($listsModify) ?
                    showTip(['msg' => '列表页修改成功！', 'data' => $this->listItem($Lists->one())]) :
                    showTip($listsModify, 0);
            }
            Html::codeType($this->codeType, $listsOne['code_type']);
            $Text = new model\Text();
            $listsOne['code'] = $Text->content($listsOne['text_id_code']);
            $listsOne['item_ids'] = $Text->content($listsOne['text_id_item_ids']);
            Html::itemType($this->itemType, $listsOne['item_ids'] != '');
            $listsOne['banner'] = $Text->content($listsOne['text_id_banner']);
            $listsOne['nav'] = $Text->content($listsOne['text_id_nav']);
            View::assign(['One' => $listsOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isView()
    {
        if (Request::isAjax() && Request::post('id')) {
            if (Config::get('app.demo')) {
                return showTip('演示站，列表页无法设置上下架！', 0);
            }
            $Lists = new model\Lists();
            $listsOne = $Lists->one();
            if (!$listsOne) {
                return showTip('不存在此列表页！', 0);
            }
            if ($listsOne['is_view'] == 0) {
                return $Lists->isView(1) ? showTip('列表页上架成功！') : showTip('列表页上架失败！', 0);
            } else {
                return $Lists->isView(0) ? showTip('列表页下架成功！') : showTip('列表页下架失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            $Lists = new model\Lists();
            $textId = [];
            if (Request::post('id')) {
                $listsOne = $Lists->one();
                if (!$listsOne) {
                    return showTip('不存在此列表页！', 0);
                }
                if (Config::get('app.demo') && Request::post('id') == 1) {
                    return showTip('演示站，id为1的列表页无法删除！', 0);
                }
                $textId[] = $listsOne['text_id_code'];
                $textId[] = $listsOne['text_id_item_ids'];
                $textId[] = $listsOne['text_id_banner'];
                $textId[] = $listsOne['text_id_nav'];
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    $listsOne = $Lists->one($value);
                    if (!$listsOne) {
                        return showTip('不存在您勾选的列表页！', 0);
                    }
                    if (Config::get('app.demo') && $value == 1) {
                        return showTip('演示站，id为1的列表页无法删除！', 0);
                    }
                    $textId[] = $listsOne['text_id_code'];
                    $textId[] = $listsOne['text_id_item_ids'];
                    $textId[] = $listsOne['text_id_banner'];
                    $textId[] = $listsOne['text_id_nav'];
                }
            }
            if ($Lists->remove()) {
                (new model\Click())->remove([Config::get('page.lists'), Config::get('page.lists_wxxcx')]);
                (new model\Text())->remove($textId);

                $WxxcxShare = new model\WxxcxShare();
                foreach ($WxxcxShare->all(Config::get('page.lists')) as $value) {
                    $qrcode = ROOT_DIR . '/download/qrcode/' . $value['id'] . '.jpg';
                    if (is_file($qrcode)) {
                        unlink($qrcode);
                    }
                }
                $WxxcxShare->remove(Config::get('page.lists'));

                return showTip('列表页删除成功！');
            } else {
                return showTip('列表页删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function ajax()
    {
        if (Request::isAjax()) {
            $data = [];
            foreach ((new model\Item())->all2() as $key => $value) {
                $data[$key]['value'] = $value['id'];
                $data[$key]['name'] = $value['name'];
                $data[$key]['selected'] = in_array($value['id'], explode(',', Request::post('item_ids')));
            }
            return json_encode($data);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        $item['width'] = keyword($item['width']);
        $item['bg_color'] = keyword($item['bg_color']);
        $item['page'] = keyword($item['page']);
        $Click = new model\Click();
        $clickOne = $Click->one(Config::get('page.lists'), Request::get('manager_id', 0), $item['id']);
        $item['click1'] = $clickOne ? $clickOne['click'] : 0;
        $clickOne2 = $Click->one(
            Config::get('page.lists_wxxcx'),
            Request::get('manager_id', 0),
            $item['id'],
            Request::get('wxxcx_id', 0)
        );
        $item['click2'] = $clickOne2 ? $clickOne2['click'] : 0;
        $item['date'] = dateFormat($item['date']);
        $item['url'] = Config::get('url.web1') . Config::get('system.index_php') .
            ($item['module'] ?: 'id/' . $item['id']) . '.html';
        $item['module'] = keyword($item['module']);
        return $item;
    }
}
