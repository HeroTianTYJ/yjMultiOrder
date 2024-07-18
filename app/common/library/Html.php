<?php

namespace app\common\library;

use app\admin\model;
use think\facade\Config;
use think\facade\View;

class Html
{
    public static function wxxcx($id = 0)
    {
        self::selectDataset((new model\Wxxcx())->all2(), 'Wxxcx', $id);
    }

    public static function category1($id = 0)
    {
        self::selectDataset((new model\Category())->all2(), 'Category', $id);
    }

    public static function category2($id = 0)
    {
        $html = '';
        $Category = new model\Category();
        foreach ($Category->all2() as $value) {
            $categoryAll2 = $Category->all2($value['id']);
            if (count($categoryAll2)) {
                $html .= '<optgroup label="' . $value['name'] . '">';
                foreach ($categoryAll2 as $v) {
                    $html .= '<option value="' . $v['id'] . '" ' . ($v['id'] == $id ? 'selected' : '') .
                        ' parent_name="' . $value['name'] . '">' . $v['name'] . '</option>';
                }
                $html .= '</optgroup>';
            }
        }
        View::assign(['Category' => $html]);
    }

    public static function wxxcxScene($id = 0)
    {
        self::selectArray(Config::get('wxxcx_scene'), 'WxxcxScene', $id);
    }

    public static function lists($id = 0)
    {
        self::selectDataset((new model\Lists())->all2(), 'Lists', $id);
    }

    public static function item($id = 0)
    {
        self::selectDataset((new model\Item())->all2(), 'Item', $id);
    }

    public static function brand($id = 0)
    {
        $html = '';
        $Category = new model\Category();
        $Brand = new model\Brand();
        foreach ($Category->all2() as $value) {
            foreach ($Category->all2($value['id']) as $v) {
                $brandAll = $Brand->all2($v['id']);
                if (count($brandAll)) {
                    $html .= '<optgroup label="' . $value['name'] . ' - ' . $v['name'] . '">';
                    foreach ($brandAll as $v2) {
                        $html .= '<option value="' . $v2['id'] . '" ' . ($v2['id'] == $id ? 'selected' : '') . '>' .
                            $v2['name'] . '</option>';
                    }
                    $html .= '</optgroup>';
                }
            }
        }
        View::assign(['Brand' => $html]);
    }

    public static function orderIsCommissionSelect($isCommission = [], $id = 0, $flag = 0)
    {
        return $flag ?
            self::selectArray($isCommission, 'IsCommission', $id) : self::selectArray($isCommission, '', $id);
    }

    public static function typeSelect($type = [], $id = 0)
    {
        self::selectArray($type, 'Type', $id);
    }

    public static function typeRadio($type = [], $id = 0)
    {
        self::radioArray($type, 'type', 'Type', $id);
    }

    public static function stateSelect($state = [], $id = 0)
    {
        self::selectArray($state, 'State', $id);
    }

    public static function product($id = 0, $flag = 0, $price = false)
    {
        $html = '';
        $productSortAll = (new model\ProductSort())->all2();
        if (count($productSortAll)) {
            $Product = new model\Product();
            $Text = new model\Text();
            foreach ($productSortAll as $value) {
                $html .= '<optgroup label="' . $value['name'] . '">';
                foreach ($Product->all2($value['id']) as $v) {
                    if ($id == 0) {
                        $html .= '<option value="' . $v['id'] . '" ' . ($v['is_default'] && $flag == 1 ?
                                'selected' : '') . ($price ? ' price="' . $v['price'] . '" attr="' .
                                $Text->content($v['text_id_attr']) . '" own_price="' .
                                str_replace('"', "'", $Text->content($v['text_id_own_price'])) . '" commission="' .
                                $v['commission'] . '"' : '') . '>' . $v['name'] . ($price ? '（' .
                                ($v['low_price'] != '0.00' && $v['high_price'] != '0.00' ? $v['low_price'] . '元～' .
                                    $v['high_price'] . '元' : $v['price'] . '元') . '）' : '') . '</option>';
                    } else {
                        $html .= '<option value="' . $v['id'] . '" ' . ($v['id'] == $id ? 'selected' : '') .
                            ($price ? ' price="' . $v['price'] . '" attr="' . $Text->content($v['text_id_attr']) .
                                '" own_price="' . str_replace('"', "'", $Text->content($v['text_id_own_price'])) .
                                '" commission="' . $v['commission'] . '"' : '') . '>' . $v['name'] . ($price ? '（' .
                                ($v['low_price'] != '0.00' && $v['high_price'] != '0.00' ? $v['low_price'] . '元～' .
                                    $v['high_price'] . '元' : $v['price'] . '元') . '）' : '') . '</option>';
                    }
                }
                $html .= '</optgroup>';
            }
        }
        if ($flag == 2) {
            return $html;
        }
        View::assign(['Product' => $html]);
        return '';
    }

    public static function orderPaymentSelect($id = 0, $flag = 0)
    {
        return self::selectArray(Config::get('payment'), $flag ? 'Payment' : '', $id);
    }

    public static function orderPaymentRadio($id = 0)
    {
        self::radioArray(Config::get('payment'), 'payment_id', 'Payment', $id);
    }

    public static function orderStateSelect($id = 0, $flag = 0)
    {
        return self::selectDataset((new model\OrderState())->all2(), $flag ? 'OrderState' : '', $id);
    }

    public static function express($id = 0, $flag = 0)
    {
        return self::selectDataset((new model\Express())->all2(), $flag ? 'Express' : '', $id);
    }

    public static function template2($id = 0, $isDefault = 0, $flag = 0)
    {
        return self::selectDataset((new model\Template())->all2(), $flag ? 'Template' : '', $id, '', $isDefault);
    }

    protected static function radioArray($array = [], $name = '', $assignName = '', $id = 0)
    {
        $html = '';
        foreach ($array as $key => $value) {
            $html .= '<div class="radio-box"><label class="' .
                (is_array($value) && isset($value[0]) ? $value[0] : '') . '"><input type="radio" name="' . $name .
                '" value="' . $key . '" ' . ($key == $id ? 'checked' : '') . '>' .
                (is_array($value) && isset($value[1]) ? $value[1] : $value) . '</label></div>';
        }
        View::assign([$assignName => $html]);
    }

    protected static function selectArray($array = [], $assignName = '', $id = 0)
    {
        $html = '';
        foreach ($array as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ($key == $id ? 'selected' : '') . ' style="color:' .
                (is_array($value) && isset($value[0]) ? $value[0] : '') . ';">' .
                (is_array($value) && isset($value[1]) ? $value[1] : $value) . '</option>';
        }
        return $assignName ? View::assign([$assignName => $html]) : $html;
    }

    protected static function selectDataset($dataset = [], $assignName = '', $id = 0, $html = '', $isDefault = 0)
    {
        foreach ($dataset as $value) {
            $html .= '<option value="' . $value['id'] . '" ' .
                ($value['id'] == $id || ($id == 0 && $isDefault && $value['is_default']) ? 'selected' : '') .
                ' style="color:' . ($value['color'] ?? '') . ';">' . $value['name'] . '</option>';
        }
        return $assignName ? View::assign([$assignName => $html]) : $html;
    }

    protected static function checkboxArray($array = [], $name = '', $assignName = '', $ids = [])
    {
        $html = '';
        foreach ($array as $key => $value) {
            $html .= '<div class="check-box"><label class="' .
                (is_array($value) && isset($value[0]) ? $value[0] : '') . '"><input type="checkbox" name="' . $name .
                '[]" value="' . $key . '" ' . (in_array($key, $ids) ? 'checked' : '') . '>' .
                (is_array($value) && isset($value[1]) ? $value[1] : $value) . '</label></div>';
        }
        return $assignName ? View::assign([$assignName => $html]) : $html;
    }
}
