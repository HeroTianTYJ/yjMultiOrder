<?php

namespace app\admin\controller;

use app\admin\model;
use app\admin\library\Html;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class Product extends Base
{
    public function index()
    {
        $productAll = (new model\Product())->all();
        if (Request::isAjax()) {
            foreach ($productAll as $key => $value) {
                $productAll[$key] = $this->listItem($value);
            }
            return $productAll->items() ? json_encode($productAll->items()) : '';
        }
        View::assign(['Total' => $productAll->total()]);
        Html::productSort(Request::get('product_sort_id'));
        return $this->view();
    }

    public function add()
    {
        if (Request::isAjax()) {
            if (Request::get('action') == 'do') {
                $productAdd = (new model\Product())->add();
                if (is_numeric($productAdd)) {
                    return $productAdd > 0 ? showTip('商品添加成功！') : showTip('商品添加失败！', 0);
                } else {
                    return showTip($productAdd, 0);
                }
            }
            Html::productSort();
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Product = new model\Product();
            $productOne = $Product->one();
            if (!$productOne) {
                return showTip('不存在此商品！', 0);
            }
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo') && Request::post('id') <= 5) {
                    return showTip('演示站，id<=5的商品无法修改！', 0);
                }
                $productModify = $Product->modify($productOne['text_id_attr'], $productOne['text_id_own_price']);
                return is_numeric($productModify) ?
                    showTip(['msg' => '商品修改成功！', 'data' => $this->listItem($Product->one())]) :
                    showTip($productModify, 0);
            }
            Html::productSort($productOne['product_sort_id']);
            $Text = new model\Text();
            $attrContent = $Text->content($productOne['text_id_attr']);
            $ownPriceContent = $Text->content($productOne['text_id_own_price']);
            $attrStr = $ownPriceStr = '';
            if ($attrContent) {
                $attr = explode('|', $attrContent);
                foreach ($attr as $key => $value) {
                    $value = explode(':', $value);
                    $attrV = explode(',', $value[1]);
                    $attrStr .= '<div><p><input type="text" name="attr_k[' . $key . ']" value="' . $value[0] .
                        '" class="text attr_k" placeholder="属性名">：　　' .
                        '<input type="button" value="删除" class="button delete_attr"></p>' .
                        '<p><input type="text" name="attr_v[' . $key . '][]" value="' . ($attrV[0] ?? '') .
                        '" class="text attr_v" placeholder="属性值"><input type="text" name="attr_v[' . $key .
                        '][]" value="' . ($attrV[1] ?? '') . '" class="text attr_v" placeholder="属性值">' .
                        '<input type="text" name="attr_v[' . $key . '][]" value="' . ($attrV[2] ?? '') .
                        '" class="text attr_v" placeholder="属性值"><input type="text" name="attr_v[' . $key .
                        '][]" value="' . ($attrV[3] ?? '') . '" class="text attr_v" placeholder="属性值">' .
                        '<input type="text" name="attr_v[' . $key . '][]" value="' . ($attrV[4] ?? '') .
                        '" class="text attr_v" placeholder="属性值"></p><p><input type="text" name="attr_v[' . $key .
                        '][]" value="' . ($attrV[5] ?? '') . '" class="text attr_v" placeholder="属性值">' .
                        '<input type="text" name="attr_v[' . $key . '][]" value="' . ($attrV[6] ?? '') .
                        '" class="text attr_v" placeholder="属性值"><input type="text" name="attr_v[' . $key .
                        '][]" value="' . ($attrV[7] ?? '') . '" class="text attr_v" placeholder="属性值">' .
                        '<input type="text" name="attr_v[' . $key . '][]" value="' . ($attrV[8] ?? '') .
                        '" class="text attr_v" placeholder="属性值"><input type="text" name="attr_v[' . $key .
                        '][]" value="' . ($attrV[9] ?? '') . '" class="text attr_v" placeholder="属性值"></p></div>';
                }
                $productOne['attr_str'] = $attrStr;
                if ($ownPriceContent) {
                    $ownPrice = json_decode($ownPriceContent);
                    if (count($attr) == 1) {
                        $temp = explode(':', $attr[0]);
                        $attrV = explode(',', $temp[1]);
                        foreach ($attrV as $key => $value) {
                            $ownPriceStr .= '<p>' . $temp[0] . ' - ' . $value . '：<input name="own_price[]" value="' .
                                (empty($ownPrice[$key]) || !is_numeric($ownPrice[$key]) ? $productOne['price'] :
                                    $ownPrice[$key]) . '" class="text" type="text"></p>';
                        }
                    } elseif (count($attr) == 2) {
                        $temp = explode(':', $attr[0]);
                        $attrV = explode(',', $temp[1]);
                        foreach ($attrV as $key => $value) {
                            $ownPriceStr .= '<p>' . $temp[0] . ' - ' . $value . '</p><div class="tree1">';
                            $temp2 = explode(':', $attr[1]);
                            $attrV2 = explode(',', $temp2[1]);
                            foreach ($attrV2 as $k => $v) {
                                $ownPriceStr .= '<p>' . $temp2[0] . ' - ' . $v . '：<input name="own_price[' . $key .
                                    '][]" value="' . (empty($ownPrice[$key][$k]) ||
                                    !is_numeric($ownPrice[$key][$k]) ? $productOne['price'] :
                                        $ownPrice[$key][$k]) . '" class="text" type="text"></p>';
                            }
                            $ownPriceStr .= '</div>';
                        }
                    } elseif (count($attr) == 3) {
                        $temp = explode(':', $attr[0]);
                        $attrV = explode(',', $temp[1]);
                        foreach ($attrV as $key => $value) {
                            $ownPriceStr .= '<p>' . $temp[0] . ' - ' . $value . '</p><div class="tree1">';
                            $temp2 = explode(':', $attr[1]);
                            $attrV2 = explode(',', $temp2[1]);
                            foreach ($attrV2 as $k => $v) {
                                $ownPriceStr .= '<p>' . $temp2[0] . ' - ' . $v . '</p><div class="tree2">';
                                $temp3 = explode(':', $attr[2]);
                                $attrV3 = explode(',', $temp3[1]);
                                foreach ($attrV3 as $k2 => $v2) {
                                    $ownPriceStr .= '<p>' . $temp3[0] . ' - ' . $v2 . '：<input name="own_price[' .
                                        $key . '][' . $k . '][]" value="' . (empty($ownPrice[$key][$k][$k2]) ||
                                        !is_numeric($ownPrice[$key][$k][$k2]) ? $productOne['price'] :
                                            $ownPrice[$key][$k][$k2]) . '" class="text" type="text"></p>';
                                }
                                $ownPriceStr .= '</div>';
                            }
                            $ownPriceStr .= '</div>';
                        }
                    } elseif (count($attr) == 4) {
                        $temp = explode(':', $attr[0]);
                        $attrV = explode(',', $temp[1]);
                        foreach ($attrV as $key => $value) {
                            $ownPriceStr .= '<p>' . $temp[0] . ' - ' . $value . '</p><div class="tree1">';
                            $temp2 = explode(':', $attr[1]);
                            $attrV2 = explode(',', $temp2[1]);
                            foreach ($attrV2 as $k => $v) {
                                $ownPriceStr .= '<p>' . $temp2[0] . ' - ' . $v . '</p><div class="tree2">';
                                $temp3 = explode(':', $attr[2]);
                                $attrV3 = explode(',', $temp3[1]);
                                foreach ($attrV3 as $k2 => $v2) {
                                    $ownPriceStr .= '<p>' . $temp3[0] . ' - ' . $v2 . '</p><div class="tree3">';
                                    $temp4 = explode(':', $attr[3]);
                                    $attrV4 = explode(',', $temp4[1]);
                                    foreach ($attrV4 as $k3 => $v3) {
                                        $ownPriceStr .= '<p>' . $temp4[0] . ' - ' . $v3 . '：<input name="own_price[' .
                                            $key . '][' . $k . '][' . $k2 . '][]" value="' .
                                            (empty($ownPrice[$key][$k][$k2][$k3]) ||
                                            !is_numeric($ownPrice[$key][$k][$k2][$k3]) ? $productOne['price'] :
                                                $ownPrice[$key][$k][$k2][$k3]) . '" class="text" type="text"></p>';
                                    }
                                    $ownPriceStr .= '</div>';
                                }
                                $ownPriceStr .= '</div>';
                            }
                            $ownPriceStr .= '</div>';
                        }
                    }
                }
            }
            $productOne['own_price_str'] = $ownPriceStr;
            View::assign(['One' => $productOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function delete()
    {
        if (Request::isAjax() && (Request::post('id') || Request::post('ids'))) {
            if (Config::get('app.demo')) {
                return showTip('演示站，商品无法删除！', 0);
            }
            $Product = new model\Product();
            $textId = [];
            if (Request::post('id')) {
                $productOne = $Product->one();
                if (!$productOne) {
                    return showTip('不存在此商品！', 0);
                }
                $textId[] = $productOne['text_id_attr'];
                $textId[] = $productOne['text_id_own_price'];
            } elseif (Request::post('ids')) {
                foreach (explode(',', Request::post('ids')) as $value) {
                    $productOne = $Product->one($value);
                    if (!$productOne) {
                        return showTip('不存在您勾选的商品！', 0);
                    }
                    $textId[] = $productOne['text_id_attr'];
                    $textId[] = $productOne['text_id_own_price'];
                }
            }
            if ($Product->remove()) {
                (new model\Text())->remove($textId);
                return showTip('商品删除成功！');
            } else {
                return showTip('商品删除失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isView()
    {
        if (Request::isAjax() && Request::post('id')) {
            if (Config::get('app.demo')) {
                return showTip('演示站，商品无法设置上下架！', 0);
            }
            $Product = new model\Product();
            $productOne = $Product->one();
            if (!$productOne) {
                return showTip('不存在此商品！', 0);
            }
            if ($productOne['is_view'] == 0) {
                return $Product->isView(1) ? showTip('商品上架成功！') : showTip('商品上架失败！', 0);
            } else {
                return $Product->isView(0) ? showTip('商品下架成功！') : showTip('商品下架失败！', 0);
            }
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function isDefault()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Product = new model\Product();
            if (!$Product->one()) {
                return showTip('不存在此商品！', 0);
            }
            return $Product->isDefault() ? showTip('设置默认商品成功！') : showTip('设置默认商品失败！', 0);
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function sort()
    {
        if (Request::isAjax()) {
            $Product = new model\Product();
            foreach (Request::post('sort') as $key => $value) {
                if (is_numeric($value)) {
                    $Product->sort($key, $value);
                }
            }
            return showTip('商品排序成功！');
        } else {
            return showTip('非法操作！', 0);
        }
    }

    private function listItem($item)
    {
        $item['name'] = keyword($item['name']);
        $productSortOne = (new model\ProductSort())->one($item['product_sort_id']);
        $item['product_sort'] = $productSortOne ?
            '<span style="color:' . $productSortOne['color'] . ';">' . $productSortOne['name'] . '</span>' :
            '此分类已被删除';
        $item['price'] = $item['low_price'] != '0.00' && $item['high_price'] != '0.00' ?
            $item['low_price'] . '元～' . $item['high_price'] . '元' : $item['price'] . '元';
        $item['price2'] = $item['price2'] . '元';
        $item['date'] = dateFormat($item['date']);
        return $item;
    }
}
