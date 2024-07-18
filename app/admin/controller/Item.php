<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\library\Json;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class Item extends Base
{
    private array $isShowPrice = ['隐藏', '显示'];
    private array $isShowSend = ['隐藏', '显示'];
    private array $isDistribution = ['否', '是'];
    private array $codeType = ['使用全局代码 + 独立代码', '仅使用独立代码'];
    private array $productType = ['单分类', '多分类'];
    private array $productViewType = ['单选按钮', '下拉框'];
    private array $column = ['抢购描述', '购买流程', '商品介绍', '客户服务', '联系方式', '客户评价', '自定义栏目1', '自定义栏目2', '自定义栏目3', '自定义栏目4',
        '自定义栏目5', '在线订购'];
    private array $commentType = ['手动输入', '留言板', '已精选的留言'];

    public function index()
    {
        $itemAll = (new model\Item())->all();
        if (Request::isAjax()) {
            foreach ($itemAll as $key => $value) {
                $itemAll[$key] = $this->listItem($value);
            }
            return $itemAll->items() ? json_encode($itemAll->items()) : '';
        }
        View::assign(['Total' => $itemAll->total()]);
        Html::brand(Request::get('brand_id'));
        Html::wxxcx(Request::get('wxxcx_id'));
        Html::manager(Request::get('manager_id'), 3);
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $itemAdd = (new model\Item())->add();
                if (is_numeric($itemAdd)) {
                    return $itemAdd > 0 ? showTip('商品页添加成功！') : showTip('商品页添加失败！', 0);
                } else {
                    return showTip($itemAdd, 0);
                }
            }
            Html::brand();
            Html::tagBg();
            Html::isShowPrice($this->isShowPrice);
            Html::isShowSend($this->isShowSend);
            Html::isDistribution($this->isDistribution);
            Html::codeType($this->codeType);
            Html::commentType($this->commentType);
            Html::messageBoard();
            Html::template2(0, 0, 1);
            Html::productType($this->productType);
            Html::productSort();
            Html::productViewType($this->productViewType);
            Html::columnSort($this->column);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function code()
    {
        return $this->view();
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Item = new model\Item();
            $itemOne = $Item->one();
            if (!$itemOne) {
                return showTip('不存在此商品页！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo') && Request::post('id') <= 4) {
                    return $this->failed('演示站，id<=4的商品页无法修改！');
                }
                $itemModify = $Item->modify($itemOne);
                return is_numeric($itemModify) ?
                    showTip(['msg' => '商品页修改成功！', 'data' => $this->listItem($Item->one())]) :
                    showTip($itemModify, 0);
            }
            $Text = new model\Text();
            Html::brand($itemOne['brand_id']);
            Html::tagBg($itemOne['tag_bg']);
            Html::isShowPrice($this->isShowPrice, $itemOne['is_show_price']);
            Html::isShowSend($this->isShowSend, $itemOne['is_show_send']);
            Html::isDistribution($this->isDistribution, $itemOne['is_distribution']);
            Html::codeType($this->codeType, $itemOne['code_type']);
            $itemOne['code'] = $Text->content($itemOne['text_id_code']);
            $itemOne['buy'] = $Text->content($itemOne['text_id_buy']);
            $itemOne['procedure'] = $Text->content($itemOne['text_id_procedure']);
            $itemOne['introduce'] = $Text->content($itemOne['text_id_introduce']);
            $itemOne['service'] = $Text->content($itemOne['text_id_service']);
            Html::commentType($this->commentType, $itemOne['comment_type'] == '' ? '-1' : $itemOne['comment_type']);
            $itemOne['comment'] = $Text->content($itemOne['text_id_comment']);
            Html::messageBoard($itemOne['message_board_id']);
            $itemOne['column_content1'] = $Text->content($itemOne['text_id_column_content1']);
            $itemOne['column_content2'] = $Text->content($itemOne['text_id_column_content2']);
            $itemOne['column_content3'] = $Text->content($itemOne['text_id_column_content3']);
            $itemOne['column_content4'] = $Text->content($itemOne['text_id_column_content4']);
            $itemOne['column_content5'] = $Text->content($itemOne['text_id_column_content5']);
            Html::template2($itemOne['template_id'], 0, 1);
            Html::productType($this->productType, $itemOne['product_type']);
            Html::productSort($itemOne['product_sort_ids']);
            Html::productViewType($this->productViewType, $itemOne['product_view_type']);
            Html::columnSort($this->column, $itemOne['sort']);
            $itemOne['nav'] = $Text->content($itemOne['text_id_nav']);
            View::assign(['One' => $itemOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update2()
    {
        if (Request::isAjax() && Request::post('id')) {
            (new model\Item())->modify2();
            return showTip('商品页添加时间修改成功！');
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isView()
    {
        if (Request::isAjax() && Request::post('id')) {
            if (Config::get('app.demo')) {
                return showTip('演示站，商品页无法设置上下架！', 0);
            }
            $Item = new model\Item();
            $itemOne = $Item->one2();
            if (!$itemOne) {
                return showTip('不存在此商品页！', 0);
            }
            if ($itemOne['is_view'] == 0) {
                return $Item->isView(1) ? showTip('商品页上架成功！') : showTip('商品页上架失败！', 0);
            } else {
                return $Item->isView(0) ? showTip('商品页下架成功！') : showTip('商品页下架失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            $Item = new model\Item();
            $textId = [];
            if (Request::post('id')) {
                $itemOne = $Item->one();
                if (!$itemOne) {
                    return showTip('不存在此商品页！', 0);
                }
                if (Config::get('app.demo') && Request::post('id') <= 4) {
                    return showTip('演示站，id<=4的商品页无法删除！', 0);
                }
                $textId[] = $itemOne['text_id_code'];
                $textId[] = $itemOne['text_id_buy'];
                $textId[] = $itemOne['text_id_procedure'];
                $textId[] = $itemOne['text_id_introduce'];
                $textId[] = $itemOne['text_id_service'];
                $textId[] = $itemOne['text_id_comment'];
                $textId[] = $itemOne['text_id_column_content1'];
                $textId[] = $itemOne['text_id_column_content2'];
                $textId[] = $itemOne['text_id_column_content3'];
                $textId[] = $itemOne['text_id_column_content4'];
                $textId[] = $itemOne['text_id_column_content5'];
                $textId[] = $itemOne['text_id_nav'];
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    $itemOne = $Item->one($value);
                    if (!$itemOne) {
                        return showTip('不存在您勾选的商品页！', 0);
                    }
                    if (Config::get('app.demo') && $value <= 4) {
                        return showTip('演示站，id<=4的商品页无法删除！', 0);
                    }
                    $textId[] = $itemOne['text_id_code'];
                    $textId[] = $itemOne['text_id_buy'];
                    $textId[] = $itemOne['text_id_procedure'];
                    $textId[] = $itemOne['text_id_introduce'];
                    $textId[] = $itemOne['text_id_service'];
                    $textId[] = $itemOne['text_id_comment'];
                    $textId[] = $itemOne['text_id_column_content1'];
                    $textId[] = $itemOne['text_id_column_content2'];
                    $textId[] = $itemOne['text_id_column_content3'];
                    $textId[] = $itemOne['text_id_column_content4'];
                    $textId[] = $itemOne['text_id_column_content5'];
                    $textId[] = $itemOne['text_id_nav'];
                }
            }
            if ($Item->remove()) {
                (new model\Click())->remove([Config::get('page.item'), Config::get('page.item_wxxcx')]);
                (new model\Text())->remove($textId);

                $WxxcxShare = new model\WxxcxShare();
                foreach ($WxxcxShare->all(Config::get('page.item')) as $value) {
                    $qrcode = ROOT_DIR . '/download/qrcode/' . $value['id'] . '.jpg';
                    if (is_file($qrcode)) {
                        unlink($qrcode);
                    }
                }
                $WxxcxShare->remove(Config::get('page.item'));

                return showTip('商品页删除成功！');
            } else {
                return showTip('商品页删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function ajaxProduct()
    {
        if (Request::isAjax()) {
            return Json::product(Request::post('product_ids1'), Request::post('product_sort_id'));
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function ajaxProduct2()
    {
        if (Request::isAjax()) {
            return Json::product2(Request::post('product_ids2'));
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        if ($item['brand_id']) {
            $brandOne = (new model\Brand())->one($item['brand_id']);
            $item['brand'] = $brandOne ?
                '<span style="color:' . $brandOne['color'] . ';">' . $brandOne['name'] . '</span>' : '此品牌已被删除';
        } else {
            $item['brand'] = '';
        }
        $item['price1'] = keyword($item['price1']);
        $item['price2'] = keyword($item['price2']);
        $item['sale'] = keyword($item['sale']);
        $item['countdown1'] = keyword($item['countdown1']);
        $item['countdown2'] = keyword($item['countdown2']);
        $Click = new model\Click();
        $clickOne = $Click->one(Config::get('page.item'), Request::get('manager_id', 0), $item['id']);
        $item['click1'] = $clickOne ? $clickOne['click'] : 0;
        $clickOne2 = $Click->one(
            Config::get('page.item_wxxcx'),
            Request::get('manager_id', 0),
            $item['id'],
            Request::get('wxxcx_id', 0)
        );
        $item['click2'] = $clickOne2 ? $clickOne2['click'] : 0;
        $item['date'] = dateFormat($item['date']);
        $item['url'] = Config::get('url.web1') . Config::get('system.index_php') . 'item/' . $item['id'] . '.html';
        return $item;
    }
}
