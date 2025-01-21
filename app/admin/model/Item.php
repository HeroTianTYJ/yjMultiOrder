<?php

namespace app\admin\model;

use app\admin\validate\Item as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Item extends Model
{
    //按不同类型查询记录数
    public function totalCount($type = 0)
    {
        try {
            $map = [];
            if ($type == 1) {
                $map['is_distribution'] = 1;
            } elseif ($type == 2) {
                $map['is_view'] = 1;
            }
            return $this->where($map)->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            $map['where'] = '(';
            foreach (['name', 'price1', 'price2', 'sale', 'countdown1', 'countdown2'] as $value) {
                $map['where'] .= '`' . $value . '` LIKE :' . $value . ' OR ';
                $map['value'][$value] = '%' . Request::get('keyword') . '%';
            }
            $map['where'] = substr($map['where'], 0, -4) . ')';
            if (Request::get('brand_id')) {
                $map['where'] .= ' AND `brand_id`=:brand_id';
                $map['value']['brand_id'] = Request::get('brand_id');
            }
            return $this->field('id,name,brand_id,preview,price1,price2,sale,countdown1,countdown2,is_view,date')
                ->where($map['where'], $map['value'])
                ->order(['date' => 'DESC'])
                ->paginate(Config::get('app.page_size'));
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（不分页）
    public function all2()
    {
        try {
            return $this->field('id,name')->order(['date' => 'DESC'])->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（小程序列表）
    public function all3($ids = '')
    {
        try {
            $all = $this->field('id,name')->where(['is_view' => 1])->order(['date' => 'DESC']);
            return $ids ? $all->where('id', 'IN', $ids)->select()->toArray() : $all->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询图片
    public function picture()
    {
        try {
            return $this->field('CONCAT(\'[img=\',`preview`,\']\'),`copyright`,`picture`,' .
                'CONCAT(\'[img=\',`share_pic`,\']\')')
                ->where('preview', '<>', '')
                ->whereOr('copyright', 'LIKE', '%[img=%')
                ->whereOr('picture', '<>', '')
                ->whereOr('share_pic', '<>', '')
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询一条
    public function one($id = 0)
    {
        try {
            return $this->field('id,name,brand_id,preview,tag,tag_bg,is_show_price,price1,price2,sale,sale_minute,' .
                'sale_count,countdown1,countdown2,is_show_send,is_distribution,title,keyword,description,width,' .
                'bg_color,copyright,code_type,text_id_code,tel,sms,qq,picture,text_id_buy,text_id_procedure,' .
                'text_id_introduce,text_id_service,message_board_id,comment_type,text_id_comment,column_name1,' .
                'text_id_column_content1,column_name2,text_id_column_content2,column_name3,text_id_column_content3,' .
                'column_name4,text_id_column_content4,column_name5,text_id_column_content5,template_id,product_type,' .
                'product_sort_ids,product_ids,product_default,product_view_type,sort,text_id_nav,icon,share_title,' .
                'share_pic,share_desc,is_view,date')
                ->where(['id' => $id ?: Request::post('id')])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function one2($id = 0)
    {
        try {
            return $this->field('name,is_view')->where(['id' => $id ?: Request::post('id')])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add()
    {
        $data = [
            'name' => Request::post('name'),
            'brand_id' => Request::post('brand_id'),
            'preview' => Request::post('preview'),
            'tag' => Request::post('tag'),
            'tag_bg' => Request::post('tag_bg'),
            'is_show_price' => Request::post('is_show_price'),
            'price1' => Request::post('price1'),
            'price2' => Request::post('price2'),
            'sale' => Request::post('sale'),
            'sale_minute' => Request::post('sale_minute'),
            'sale_count' => Request::post('sale_count'),
            'countdown1' => Request::post('countdown1'),
            'countdown2' => Request::post('countdown2'),
            'is_show_send' => Request::post('is_show_send'),
            'is_distribution' => Request::post('is_distribution'),
            'title' => Request::post('title'),
            'keyword' => Request::post('keyword'),
            'description' => Request::post('description'),
            'width' => Request::post('width'),
            'bg_color' => Request::post('bg_color'),
            'copyright' => Request::post('copyright', '', 'stripslashes'),
            'code_type' => Request::post('code_type'),
            'code' => Request::post('code', '', 'stripslashes'),
            'tel' => Request::post('tel'),
            'sms' => Request::post('sms'),
            'qq' => Request::post('qq'),
            'picture' => Request::post('picture'),
            'buy' => unescapePic(Request::post('buy', '', 'stripslashes')),
            'procedure' => unescapePic(Request::post('procedure', '', 'stripslashes')),
            'introduce' => unescapePic(Request::post('introduce', '', 'stripslashes')),
            'service' => unescapePic(Request::post('service', '', 'stripslashes')),
            'message_board_id' => Request::post('message_board_id', 0),
            'comment_type' => implode(',', Request::post('comment_type', [])),
            'comment' => Request::post('comment'),
            'column_name1' => Request::post('column_name1'),
            'column_content1' => unescapePic(Request::post('column_content1', '', 'stripslashes')),
            'column_name2' => Request::post('column_name2'),
            'column_content2' => unescapePic(Request::post('column_content2', '', 'stripslashes')),
            'column_name3' => Request::post('column_name3'),
            'column_content3' => unescapePic(Request::post('column_content3', '', 'stripslashes')),
            'column_name4' => Request::post('column_name4'),
            'column_content4' => unescapePic(Request::post('column_content4', '', 'stripslashes')),
            'column_name5' => Request::post('column_name5'),
            'column_content5' => unescapePic(Request::post('column_content5', '', 'stripslashes')),
            'template_id' => Request::post('template_id'),
            'product_type' => Request::post('product_type'),
            'product_view_type' => Request::post('product_view_type'),
            'sort' => implode(',', Request::post('sort')),
            'nav' => trim(Request::post('nav', '', 'stripslashes')),
            'icon' => trim(Request::post('icon')),
            'share_title' => Request::post('share_title'),
            'share_pic' => Request::post('share_pic'),
            'share_desc' => Request::post('share_desc'),
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if ($data['brand_id'] && !(new Brand())->one($data['brand_id'])) {
                return '您选择的品牌不存在！';
            }
            if ($data['message_board_id'] && !(new MessageBoard())->one($data['message_board_id'])) {
                return '您选择的留言板不存在！';
            }
            if ($data['template_id']) {
                if (!(new Template())->one($data['template_id'])) {
                    return '您选择的下单模板不存在！';
                }
                if ($data['product_type'] == 0) {
                    if (!Request::post('product_ids1')) {
                        return '请至少选择一个商品！';
                    }
                    $data['product_sort_ids'] = Request::post('product_sort_id');
                    $data['product_ids'] = Request::post('product_ids1');
                    $data['product_default'] = Request::post('product_default1');
                } elseif ($data['product_type'] == 1) {
                    if (!Request::post('product_ids2')) {
                        return '请至少选择一个商品！';
                    }
                    $data['product_sort_ids'] = Request::post('product_sort_ids');
                    $data['product_ids'] = Request::post('product_ids2');
                    $data['product_default'] = Request::post('product_default2');
                }
                foreach (explode(',', $data['product_sort_ids']) as $value) {
                    if (!(new ProductSort())->one($value)) {
                        return '您勾选的商品分类不存在！';
                    }
                }
                foreach (explode(',', $data['product_ids']) as $value) {
                    if (!(new Product())->one($value)) {
                        return '您勾选的商品不存在！';
                    }
                }
                if (!in_array($data['product_default'], explode(',', $data['product_ids']))) {
                    return '您选择的默认商品不存在！';
                }
            }
            if (count(explode("\r\n", $data['nav'])) > 3) {
                return '最多只能设置3个底部导航链接！';
            }
            if (count(explode("\r\n", $data['icon'])) > 3) {
                return '最多只能设置3个底部导航图标！';
            }
            $Text = new Text();
            $data['text_id_code'] = $Text->amr($data['code']);
            $data['text_id_buy'] = $Text->amr($data['buy']);
            $data['text_id_procedure'] = $Text->amr($data['procedure']);
            $data['text_id_introduce'] = $Text->amr($data['introduce']);
            $data['text_id_service'] = $Text->amr($data['service']);
            $data['text_id_comment'] = $Text->amr($data['comment']);
            $data['text_id_column_content1'] = $Text->amr($data['column_content1']);
            $data['text_id_column_content2'] = $Text->amr($data['column_content2']);
            $data['text_id_column_content3'] = $Text->amr($data['column_content3']);
            $data['text_id_column_content4'] = $Text->amr($data['column_content4']);
            $data['text_id_column_content5'] = $Text->amr($data['column_content5']);
            $data['text_id_nav'] = $Text->amr($data['nav']);
            unset($data['code'], $data['buy'], $data['procedure'], $data['introduce'], $data['service']);
            unset($data['comment'], $data['column_content1'], $data['column_content2'], $data['column_content3']);
            unset($data['column_content4'], $data['column_content5'], $data['nav']);
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //修改
    public function modify($one)
    {
        $data = [
            'name' => Request::post('name'),
            'brand_id' => Request::post('brand_id'),
            'tag' => Request::post('tag'),
            'tag_bg' => Request::post('tag_bg'),
            'preview' => Request::post('preview'),
            'is_show_price' => Request::post('is_show_price'),
            'price1' => Request::post('price1'),
            'price2' => Request::post('price2'),
            'sale' => Request::post('sale'),
            'sale_minute' => Request::post('sale_minute'),
            'sale_count' => Request::post('sale_count'),
            'countdown1' => Request::post('countdown1'),
            'countdown2' => Request::post('countdown2'),
            'is_show_send' => Request::post('is_show_send'),
            'is_distribution' => Request::post('is_distribution'),
            'title' => Request::post('title'),
            'keyword' => Request::post('keyword'),
            'description' => Request::post('description'),
            'width' => Request::post('width'),
            'bg_color' => Request::post('bg_color'),
            'copyright' => Request::post('copyright', '', 'stripslashes'),
            'code_type' => Request::post('code_type'),
            'code' => Request::post('code', '', 'stripslashes'),
            'tel' => Request::post('tel'),
            'sms' => Request::post('sms'),
            'qq' => Request::post('qq'),
            'picture' => Request::post('picture'),
            'buy' => unescapePic(Request::post('buy', '', 'stripslashes')),
            'procedure' => unescapePic(Request::post('procedure', '', 'stripslashes')),
            'introduce' => unescapePic(Request::post('introduce', '', 'stripslashes')),
            'service' => unescapePic(Request::post('service', '', 'stripslashes')),
            'message_board_id' => Request::post('message_board_id', 0),
            'comment_type' => implode(',', Request::post('comment_type', [])),
            'comment' => Request::post('comment'),
            'column_name1' => Request::post('column_name1'),
            'column_content1' => unescapePic(Request::post('column_content1', '', 'stripslashes')),
            'column_name2' => Request::post('column_name2'),
            'column_content2' => unescapePic(Request::post('column_content2', '', 'stripslashes')),
            'column_name3' => Request::post('column_name3'),
            'column_content3' => unescapePic(Request::post('column_content3', '', 'stripslashes')),
            'column_name4' => Request::post('column_name4'),
            'column_content4' => unescapePic(Request::post('column_content4', '', 'stripslashes')),
            'column_name5' => Request::post('column_name5'),
            'column_content5' => unescapePic(Request::post('column_content5', '', 'stripslashes')),
            'template_id' => Request::post('template_id'),
            'product_type' => Request::post('product_type'),
            'product_view_type' => Request::post('product_view_type'),
            'sort' => implode(',', Request::post('sort')),
            'nav' => trim(Request::post('nav', '', 'stripslashes')),
            'icon' => trim(Request::post('icon')),
            'share_title' => Request::post('share_title'),
            'share_pic' => Request::post('share_pic'),
            'share_desc' => Request::post('share_desc')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if ($data['brand_id'] && !(new Brand())->one($data['brand_id'])) {
                return '您选择的品牌不存在！';
            }
            if ($data['message_board_id'] && !(new MessageBoard())->one($data['message_board_id'])) {
                return '您选择的留言板不存在！';
            }
            if ($data['template_id']) {
                if (!(new Template())->one($data['template_id'])) {
                    return '您选择的下单模板不存在！';
                }
                if ($data['product_type'] == 0) {
                    if (!Request::post('product_ids1')) {
                        return '请至少选择一个商品！';
                    }
                    $data['product_sort_ids'] = Request::post('product_sort_id');
                    $data['product_ids'] = Request::post('product_ids1');
                    $data['product_default'] = Request::post('product_default1');
                } elseif ($data['product_type'] == 1) {
                    if (!Request::post('product_ids2')) {
                        return '请至少选择一个商品！';
                    }
                    $data['product_sort_ids'] = Request::post('product_sort_ids');
                    $data['product_ids'] = Request::post('product_ids2');
                    $data['product_default'] = Request::post('product_default2');
                }
                foreach (explode(',', $data['product_sort_ids']) as $value) {
                    if (!(new ProductSort())->one($value)) {
                        return '您勾选的商品分类不存在！';
                    }
                }
                foreach (explode(',', $data['product_ids']) as $value) {
                    if (!(new Product())->one($value)) {
                        return '您勾选的商品不存在！';
                    }
                }
                if (!in_array($data['product_default'], explode(',', $data['product_ids']))) {
                    return '您选择的默认商品不存在！';
                }
            }
            if (count(explode("\r\n", $data['nav'])) > 3) {
                return '最多只能设置3个底部导航链接！';
            }
            if (count(explode("\r\n", $data['icon'])) > 3) {
                return '最多只能设置3个底部导航图标！';
            }
            $Text = new Text();
            $data['text_id_code'] = $Text->amr($data['code'], $one['text_id_code']);
            $data['text_id_buy'] = $Text->amr($data['buy'], $one['text_id_buy']);
            $data['text_id_procedure'] = $Text->amr($data['procedure'], $one['text_id_procedure']);
            $data['text_id_introduce'] = $Text->amr($data['introduce'], $one['text_id_introduce']);
            $data['text_id_service'] = $Text->amr($data['service'], $one['text_id_service']);
            $data['text_id_comment'] = $Text->amr($data['comment'], $one['text_id_comment']);
            $data['text_id_column_content1'] = $Text->amr($data['column_content1'], $one['text_id_column_content1']);
            $data['text_id_column_content2'] = $Text->amr($data['column_content2'], $one['text_id_column_content2']);
            $data['text_id_column_content3'] = $Text->amr($data['column_content3'], $one['text_id_column_content3']);
            $data['text_id_column_content4'] = $Text->amr($data['column_content4'], $one['text_id_column_content4']);
            $data['text_id_column_content5'] = $Text->amr($data['column_content5'], $one['text_id_column_content5']);
            $data['text_id_nav'] = $Text->amr($data['nav'], $one['text_id_nav']);
            unset($data['code'], $data['buy'], $data['procedure'], $data['introduce'], $data['service']);
            unset($data['comment'], $data['column_content1'], $data['column_content2'], $data['column_content3']);
            unset($data['column_content4'], $data['column_content5'], $data['nav']);
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return $validate->getError();
        }
    }
    public function modify2()
    {
        return $this->where('1=1')->update(['date' => time()]);
    }

    //确认和取消显示
    public function isView($isView)
    {
        return $this->where(['id' => Request::post('id')])->update(['is_view' => $isView]);
    }

    //删除
    public function remove()
    {
        try {
            $affectedRows = $this->where('id', 'IN', Request::post('id') ?: Request::post('ids'))->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
