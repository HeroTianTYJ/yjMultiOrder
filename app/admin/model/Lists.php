<?php

namespace app\admin\model;

use app\admin\validate\Lists as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Lists extends Model
{
    private array $hold = ['base', 'brand', 'category', 'common', 'error', 'index', 'message', 'order', 'pay',
        'suborder'];

    //按是否运作查询记录数
    public function totalCount($isView = 0)
    {
        try {
            return $isView ? $this->where(['is_view' => 1])->count() : $this->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            return $this->field('id,name,module,width,bg_color,page,is_view,date')
                ->where('name|module|width|bg_color|page', 'LIKE', '%' . Request::get('keyword') . '%')
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

    //查询图片
    public function picture()
    {
        try {
            return $this->field('`copyright`,`icon`,CONCAT(\'[img=\',`share_pic`,\']\')')
                ->where('copyright|icon', 'LIKE', '%[img=%')
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
            return $this->field('id,name,module,title,keyword,description,width,bg_color,copyright,code_type,' .
                'text_id_code,text_id_item_ids,page,text_id_banner,text_id_nav,icon,share_title,share_pic,share_desc,' .
                'is_view,date')
                ->where(['id' => $id ?: Request::post('id')])
                ->find();
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
            'module' => Request::post('module'),
            'title' => Request::post('title'),
            'keyword' => Request::post('keyword'),
            'description' => Request::post('description'),
            'width' => Request::post('width'),
            'bg_color' => Request::post('bg_color'),
            'copyright' => Request::post('copyright', '', null),
            'code_type' => Request::post('code_type'),
            'code' => Request::post('code', '', null),
            'item_ids' => Request::post('item_type') == 1 ? Request::post('item_ids') : '',
            'page' => Request::post('page'),
            'banner' => Request::post('banner'),
            'nav' => trim(Request::post('nav', '', null)),
            'icon' => trim(Request::post('icon')),
            'share_title' => Request::post('share_title'),
            'share_pic' => Request::post('share_pic'),
            'share_desc' => Request::post('share_desc'),
            'date' => time()
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (in_array(strtolower($data['module']), $this->hold)) {
                return $data['module'] . '为保留页面地址，请重新输入！';
            }
            if (count(explode("\r\n", $data['nav'])) > 3) {
                return '最多只能设置3个底部导航链接！';
            }
            if (count(explode("\r\n", $data['icon'])) > 3) {
                return '最多只能设置3个底部导航图标！';
            }
            if ($data['module'] && $this->repeat()) {
                return '此页面地址已存在！';
            }
            $Text = new Text();
            $data['text_id_code'] = $Text->amr($data['code']);
            $data['text_id_item_ids'] = $Text->amr($data['item_ids']);
            $data['text_id_banner'] = $Text->amr($data['banner']);
            $data['text_id_nav'] = $Text->amr($data['nav']);
            unset($data['code'], $data['item_ids'], $data['nav'], $data['banner']);
            return $this->insertGetId($data);
        } else {
            return $validate->getError();
        }
    }

    //修改
    public function modify($textIdCode = 0, $textIdItemIds = 0, $textIdBanner = 0, $textIdNav = 0)
    {
        $data = [
            'name' => Request::post('name'),
            'module' => Request::post('module'),
            'title' => Request::post('title'),
            'keyword' => Request::post('keyword'),
            'description' => Request::post('description'),
            'width' => Request::post('width'),
            'bg_color' => Request::post('bg_color'),
            'copyright' => Request::post('copyright', '', null),
            'code_type' => Request::post('code_type'),
            'code' => Request::post('code', '', null),
            'item_ids' => Request::post('item_type') == 1 ? Request::post('item_ids') : '',
            'page' => Request::post('page'),
            'banner' => Request::post('banner'),
            'nav' => trim(Request::post('nav', '', null)),
            'icon' => trim(Request::post('icon')),
            'share_title' => Request::post('share_title'),
            'share_pic' => Request::post('share_pic'),
            'share_desc' => Request::post('share_desc')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (in_array(strtolower($data['module']), $this->hold)) {
                return $data['module'] . '为保留页面地址，请重新输入！';
            }
            if (count(explode("\r\n", $data['nav'])) > 3) {
                return '最多只能设置3个底部导航链接！';
            }
            if (count(explode("\r\n", $data['icon'])) > 3) {
                return '最多只能设置3个底部导航图标！';
            }
            if ($data['module'] && $this->repeat(true)) {
                return '此页面地址已存在！';
            }
            $Text = new Text();
            $data['text_id_code'] = $Text->amr($data['code'], $textIdCode);
            $data['text_id_item_ids'] = $Text->amr($data['item_ids'], $textIdItemIds);
            $data['text_id_banner'] = $Text->amr($data['banner'], $textIdBanner);
            $data['text_id_nav'] = $Text->amr($data['nav'], $textIdNav);
            unset($data['code'], $data['item_ids'], $data['banner'], $data['nav']);
            return $this->where(['id' => Request::post('id')])->update($data);
        } else {
            return $validate->getError();
        }
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

    //验证重复
    private function repeat($update = false)
    {
        try {
            $one = $this->field('id')->where(['module' => Request::post('module')]);
            return $update ?
                $one->where('id', '<>', Request::post('id'))->find() :
                $one->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
