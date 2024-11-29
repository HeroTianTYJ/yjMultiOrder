<?php

namespace app\admin\model;

use app\admin\validate\Bill as validate;
use Exception;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class Bill extends Model
{
    //查询每个分类的总记录
    public function totalCount($billSortId)
    {
        try {
            return $this->where(['bill_sort_id' => $billSortId])->count();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有
    public function all()
    {
        try {
            return $this->field('id,manager_id,name,bill_sort_id,in_price,in_count,in_price_out,in_count_out,' .
                'out_price,out_count,out_price_in,out_count_in,all_in,all_out,all_add,date')
                ->where($this->map()['where'], $this->map()['value'])
                ->orderRaw($this->orderBy())
                ->order(['id' => 'DESC'])
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
            return $this->field('id,manager_id,name,bill_sort_id,in_price,in_count,in_price_out,in_count_out,' .
                'out_price,out_count,out_price_in,out_count_in,all_in,all_out,all_add,text_id_note,date')
                ->where($this->map()['where'], $this->map()['value'])
                ->orderRaw($this->orderBy())
                ->order(['id' => 'DESC'])
                ->select()
                ->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询所有（不分页，IN）
    public function all3()
    {
        try {
            return $this->field('id,manager_id,name,bill_sort_id,in_price,in_count,in_price_out,in_count_out,' .
                'out_price,out_count,out_price_in,out_count_in,all_in,all_out,all_add,text_id_note,date')
                ->where($this->managerId())
                ->where('id', 'IN', Request::post('ids'))
                ->orderRaw($this->orderBy())
                ->order(['id' => 'DESC'])
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
            return $this->field('id,manager_id,name,bill_sort_id,in_price,in_count,in_price_out,in_count_out,' .
                'out_price,out_count,out_price_in,out_count_in,all_in,all_out,all_add,text_id_note,date')
                ->where(['id' => $id ?: Request::post('id')])
                ->where($this->managerId())
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询最老一条
    public function older()
    {
        try {
            return $this->field('date')->where($this->managerId())->order(['date' => 'ASC'])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //查询最新一条
    public function newer()
    {
        try {
            return $this->field('all_in,all_out,all_add,date')
                ->where($this->managerId())
                ->order(['date' => 'DESC'])
                ->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
    public function newer2()
    {
        try {
            return $this->field('all_in,all_out,all_add')->order(['date' => 'DESC'])->find();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //按自定义时间统计
    public function diyTime($time1 = 0, $time2 = 0)
    {
        try {
            $map = $this->map();
            if ($time1 && $time2) {
                $map['where'] .= ' AND `date`>=:date3 AND `date`<=:date4';
                $map['value']['date3'] = strtotime($time1 . ' 00:00:00') . '';
                $map['value']['date4'] = strtotime($time2 . ' 23:59:59') . '';
            }
            return $this->field('COUNT(CASE WHEN `in_price`>0 THEN `id` END) `all_in_count`,' .
                'CASE WHEN SUM(`in_price`*`in_count`-`in_price_out`*`in_count_out`)<>0 THEN SUM' .
                '(`in_price`*`in_count`-`in_price_out`*`in_count_out`) ELSE 0.00 END `all_in_price`,' .
                'COUNT(CASE WHEN `out_price`>0 THEN `id` END) `all_out_count`,' .
                'CASE WHEN SUM(`out_price`*`out_count`-`out_price_in`*`out_count_in`)<>0 THEN SUM(`out_price`*' .
                '`out_count`-`out_price_in`*`out_count_in`) ELSE 0.00 END `all_out_price`,COUNT(*) `all_add_count`,' .
                'CASE WHEN SUM(`in_price`*`in_count`-`in_price_out`*`in_count_out`)-SUM(`out_price`*`out_count`-' .
                '`out_price_in`*`out_count_in`)<>0 THEN SUM(`in_price`*`in_count`-`in_price_out`*`in_count_out`-' .
                '`out_price`*`out_count`-`out_price_in`*`out_count_in`) ELSE 0.00 END `all_add_price`')
                ->where($map['where'], $map['value'])
                ->select()
                ->toArray()[0];
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //按天、月、年统计
    public function dayMonthYear($time, $paginate = true)
    {
        try {
            switch (Request::get('order')) {
                case 1:
                    $order = 'all_in_count';
                    break;
                case 2:
                    $order = 'all_in_price';
                    break;
                case 3:
                    $order = 'all_out_count';
                    break;
                case 4:
                    $order = 'all_out_price';
                    break;
                case 5:
                    $order = 'all_add_count';
                    break;
                case 6:
                    $order = 'all_add_price';
                    break;
                default:
                    $order = 'time';
            }
            $all = $this->field('COUNT(CASE WHEN `in_price`>0 THEN `id` END) `all_in_count`,' .
                'SUM(`in_price`*`in_count`-`in_price_out`*`in_count_out`) `all_in_price`,' .
                'COUNT(CASE WHEN `out_price`>0 THEN `id` END) `all_out_count`,' .
                'SUM(`out_price`*`out_count`-`out_price_in`*`out_count_in`) `all_out_price`,COUNT(*) `all_add_count`,' .
                'SUM(`in_price`*`in_count`-`in_price_out`*`in_count_out`)-SUM(`out_price`*`out_count`-`out_price_in`*' .
                '`out_count_in`) `all_add_price`,FROM_UNIXTIME(`date`,\'' . $time . '\') `time`')
                ->group('FROM_UNIXTIME(`date`,\'' . $time . '\')')
                ->where($this->map()['where'], $this->map()['value'])
                ->order([$order => 'DESC']);
            return $paginate ? $all->paginate(Config::get('app.page_size')) : $all->select()->toArray();
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //添加
    public function add()
    {
        $data = [
            'manager_id' => Session::get(Config::get('system.session_key_admin') . '.manage_info.id'),
            'name' => Request::post('name'),
            'bill_sort_id' => Request::post('bill_sort_id'),
            'in_price' => Request::post('in_price') ? Request::post('in_price') : 0,
            'in_count' => Request::post('in_price') ? Request::post('in_count') ? Request::post('in_count') : 1 : 0,
            'in_price_out' => Request::post('in_price_out') ? Request::post('in_price_out') : 0,
            'in_count_out' => Request::post('in_price_out') ?
                Request::post('in_count_out') ? Request::post('in_count_out') : 1 : 0,
            'out_price' => Request::post('out_price') ? Request::post('out_price') : 0,
            'out_count' => Request::post('out_price') ? Request::post('out_count') ? Request::post('out_count') : 1 : 0,
            'out_price_in' => Request::post('out_price_in') ? Request::post('out_price_in') : 0,
            'out_count_in' => Request::post('out_price_in') ?
                Request::post('out_count_in') ? Request::post('out_count_in') : 1 : 0,
            'note' => Request::post('note'),
            'date' => checkTime(Request::post('date'))
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (!(new BillSort())->one($data['bill_sort_id'])) {
                return '您选择的账单分类不存在！';
            }
            $newer = $this->newer();
            $data['all_in'] = ($newer ? $newer['all_in'] : 0) + $data['in_price'] * $data['in_count'] -
                $data['in_price_out'] * $data['in_count_out'];
            $data['all_out'] = ($newer ? $newer['all_out'] : 0) + $data['out_price'] * $data['out_count'] -
                $data['out_price_in'] * $data['out_count_in'];
            $data['all_add'] = $data['all_in'] - $data['all_out'];
            $data['text_id_note'] = (new Text())->amr($data['note']);
            unset($data['note']);
            return $this->insertGetId($data);
        } else {
            return implode($validate->getError());
        }
    }
    public function add2(
        $managerId = 0,
        $name = '',
        $inPrice = 0,
        $inCount = 0,
        $inPriceOut = 0,
        $inCountOut = 0
    ) {
        $data = [
            'manager_id' => $managerId ?: 1,
            'name' => $name,
            'bill_sort_id' => 1,
            'in_price' => $inPrice,
            'in_count' => $inCount,
            'in_price_out' => $inPriceOut,
            'in_count_out' => $inCountOut,
            'date' => time()
        ];
        $newer = $this->newer2();
        $data['all_in'] = $data['all_add'] = ($newer ? $newer['all_in'] : 0) + $data['in_price'] * $data['in_count'] -
            $data['in_price_out'] * $data['in_count_out'];
        return $this->insertGetId($data);
    }

    //修改
    public function modify($textIdNote = 0)
    {
        $data = [
            'name' => Request::post('name'),
            'bill_sort_id' => Request::post('bill_sort_id'),
            'in_price' => Request::post('in_price') ? Request::post('in_price') : 0,
            'in_count' => Request::post('in_price') ? Request::post('in_count') ? Request::post('in_count') : 1 : 0,
            'in_price_out' => Request::post('in_price_out') ? Request::post('in_price_out') : 0,
            'in_count_out' => Request::post('in_price_out') ?
                Request::post('in_count_out') ? Request::post('in_count_out') : 1 : 0,
            'out_price' => Request::post('out_price') ? Request::post('out_price') : 0,
            'out_count' => Request::post('out_price') ? Request::post('out_count') ? Request::post('out_count') : 1 : 0,
            'out_price_in' => Request::post('out_price_in') ? Request::post('out_price_in') : 0,
            'out_count_in' => Request::post('out_price_in') ?
                Request::post('out_count_in') ? Request::post('out_count_in') : 1 : 0,
            'note' => Request::post('note'),
            'date' => checkTime(Request::post('date'))
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            if (!(new BillSort())->one($data['bill_sort_id'])) {
                return '您选择的账单分类不存在！';
            }
            $data['text_id_note'] = (new Text())->amr($data['note'], $textIdNote);
            unset($data['note']);
            return $this->where(['id' => Request::post('id')])->where($this->managerId())->update($data);
        } else {
            return implode($validate->getError());
        }
    }

    //更新统计
    public function modify2($rows)
    {
        try {
            if (!is_numeric($rows)) {
                return '更新条数必须是数字！';
            }
            if ($this->count() > $rows) {
                $rows2 = $this->count() - $rows;
                $all = $this->field('id,in_price,in_count,in_price_out,in_count_out,out_price,out_count,' .
                    'out_price_in,out_count_in,all_in,all_out')
                    ->order(['date' => 'ASC', 'id' => 'ASC'])
                    ->limit($rows2, 1)
                    ->select()
                    ->toArray();
                $in = $all[0]['all_in'];
                $out = $all[0]['all_out'];
                $all2 = $this->field('id,in_price,in_count,in_price_out,in_count_out,out_price,out_count,' .
                    'out_price_in,out_count_in,all_in,all_out')
                    ->where('id', '>', $all[0]['id'])
                    ->order(['date' => 'ASC', 'id' => 'ASC'])
                    ->select()
                    ->toArray();
            } else {
                $all2 = $this->field('id,in_price,in_count,in_price_out,in_count_out,out_price,out_count,' .
                    'out_price_in,out_count_in,all_in,all_out')
                    ->order(['date' => 'ASC', 'id' => 'ASC'])
                    ->select()
                    ->toArray();
                $in = 0;
                $out = 0;
            }
            foreach ($all2 as $value) {
                $in += $value['in_price'] * $value['in_count'] - $value['in_price_out'] * $value['in_count_out'];
                $out += $value['out_price'] * $value['out_count'] - $value['out_price_in'] * $value['out_count_in'];
                $add = $in - $out;
                $data = [
                    'all_in' => $in,
                    'all_out' => $out,
                    'all_add' => $add
                ];
                $this->where(['id' => $value['id']])->update($data);
            }
            return 1;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //删除
    public function remove()
    {
        try {
            $affectedRows = $this->where('id', 'IN', Request::post('id') ?: Request::post('ids'))
                ->where($this->managerId())
                ->delete();
            if ($affectedRows) {
                Db::execute('OPTIMIZE TABLE `' . $this->getTable() . '`');
            }
            return $affectedRows;
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    //排序
    private function orderBy()
    {
        $order = 'date';
        switch (Request::get('order')) {
            case 1:
                $order = '`in_price`*`in_count`';
                break;
            case 2:
                $order = '`in_price_out`*`in_count_out`';
                break;
            case 3:
                $order = '`out_price`*`out_count`';
                break;
            case 4:
                $order = '`out_price_in`*`out_count_in`';
                break;
            case 5:
                $order = '`in_price`*`in_count`-`in_price_out`*`in_count_out`-`out_price`*`out_count`+' .
                    '`out_price_in`*`out_count_in`';
                break;
            case 6:
                $order = '`all_in`';
                break;
            case 7:
                $order = '`all_out`';
                break;
            case 8:
                $order = '`all_add`';
                break;
        }
        return $order . ' ' . (Request::get('by', 1) ? 'DESC' : 'ASC');
    }

    //高级搜索
    private function map()
    {
        $map['where'] = '`name` LIKE :name';
        $map['value']['name'] = '%' . Request::get('keyword') . '%';
        if (Request::get('manager_id')) {
            $map['where'] .= ' AND `manager_id`=:manager_id';
            $map['value']['manager_id'] = Request::get('manager_id');
        }
        if (Request::get('bill_sort_id')) {
            $map['where'] .= ' AND `bill_sort_id`=:bill_sort_id';
            $map['value']['bill_sort_id'] = Request::get('bill_sort_id');
        }
        if (Request::get('in_price1')) {
            $map['where'] .= ' AND `in_price`>=:in_price1';
            $map['value']['in_price1'] = Request::get('in_price1');
        }
        if (Request::get('in_price2')) {
            $map['where'] .= ' AND `in_price`<=:in_price2';
            $map['value']['in_price2'] = Request::get('in_price2');
        }
        if (Request::get('in_count1')) {
            $map['where'] .= ' AND `in_count`>=:in_count1';
            $map['value']['in_count1'] = Request::get('in_count1');
        }
        if (Request::get('in_count2')) {
            $map['where'] .= ' AND `in_count`<=:in_count2';
            $map['value']['in_count2'] = Request::get('in_count2');
        }
        if (Request::get('in_price_out1')) {
            $map['where'] .= ' AND `in_price_out`>=:in_price_out1';
            $map['value']['in_price_out1'] = Request::get('in_price_out1');
        }
        if (Request::get('in_price_out2')) {
            $map['where'] .= ' AND `in_price_out`<=:in_price_out2';
            $map['value']['in_price_out2'] = Request::get('in_price_out2');
        }
        if (Request::get('in_count_out1')) {
            $map['where'] .= ' AND `in_count_out`>=:in_count_out1';
            $map['value']['in_count_out1'] = Request::get('in_count_out1');
        }
        if (Request::get('in_count_out2')) {
            $map['where'] .= ' AND `in_count_out`<=:in_count_out2';
            $map['value']['in_count_out2'] = Request::get('in_count_out2');
        }
        if (Request::get('out_price1')) {
            $map['where'] .= ' AND `out_price`>=:out_price1';
            $map['value']['out_price1'] = Request::get('out_price1');
        }
        if (Request::get('out_price2')) {
            $map['where'] .= ' AND `out_price`<=:out_price2';
            $map['value']['out_price2'] = Request::get('out_price2');
        }
        if (Request::get('out_count1')) {
            $map['where'] .= ' AND `out_count`>=:out_count1';
            $map['value']['out_count1'] = Request::get('out_count1');
        }
        if (Request::get('out_count2')) {
            $map['where'] .= ' AND `out_count`<=:out_count2';
            $map['value']['out_count2'] = Request::get('out_count2');
        }
        if (Request::get('out_price_in1')) {
            $map['where'] .= ' AND `out_price_in`>=:out_price_in1';
            $map['value']['out_price_in1'] = Request::get('out_price_in1');
        }
        if (Request::get('out_price_in2')) {
            $map['where'] .= ' AND `out_price_in`<=:out_price_in2';
            $map['value']['out_price_in2'] = Request::get('out_price_in2');
        }
        if (Request::get('out_count_in1')) {
            $map['where'] .= ' AND `out_count_in`>=:out_count_in1';
            $map['value']['out_count_in1'] = Request::get('out_count_in1');
        }
        if (Request::get('out_count_in2')) {
            $map['where'] .= ' AND `out_count_in`<=:out_count_in2';
            $map['value']['out_count_in2'] = Request::get('out_count_in2');
        }
        if (Request::get('add1')) {
            $map['where'] .= ' AND `in_price`*`in_count`-`in_price_out`*`in_count_out`-`out_price`*`out_count`+' .
                '`out_price_in`*`out_count_in`>=:add1';
            $map['value']['add1'] = Request::get('add1');
        }
        if (Request::get('add2')) {
            $map['where'] .= ' AND `in_price`*`in_count`-`in_price_out`*`in_count_out`-`out_price`*`out_count`+' .
                '`out_price_in`*`out_count_in`<=:add2';
            $map['value']['add2'] = Request::get('add2');
        }
        if (Request::get('all_in1')) {
            $map['where'] .= ' AND `all_in`>=:all_in1';
            $map['value']['all_in1'] = Request::get('all_in1');
        }
        if (Request::get('all_in2')) {
            $map['where'] .= ' AND `all_in`<=:all_in2';
            $map['value']['all_in2'] = Request::get('all_in2');
        }
        if (Request::get('all_out1')) {
            $map['where'] .= ' AND `all_out`>=:all_out1';
            $map['value']['all_out1'] = Request::get('all_out1');
        }
        if (Request::get('all_out2')) {
            $map['where'] .= ' AND `all_out`<=:all_out2';
            $map['value']['all_out2'] = Request::get('all_out2');
        }
        if (Request::get('all_add1')) {
            $map['where'] .= ' AND `all_add`>=:all_add1';
            $map['value']['all_add1'] = Request::get('all_add1');
        }
        if (Request::get('all_add2')) {
            $map['where'] .= ' AND `all_add`<=:all_add2';
            $map['value']['all_add2'] = Request::get('all_add2');
        }
        if (Request::get('date1')) {
            $map['where'] .= ' AND `date`>=:date1';
            $map['value']['date1'] = strtotime(Request::get('date1') . ' 00:00:00');
        }
        if (Request::get('date2')) {
            $map['where'] .= ' AND `date`<=:date2';
            $map['value']['date2'] = strtotime(Request::get('date2') . ' 23:59:59');
        }
        $map['where'] .= ' AND ' . $this->managerId();
        return $map;
    }

    //管理权限
    private function managerId()
    {
        $session = Session::get(Config::get('system.session_key_admin') . '.manage_info');
        $sqlWhere = [
            1 => '`manager_id`=' . $session['id'],
            2 => '1=1'
        ];
        return $session['level'] != 1 ? $sqlWhere[$session['bill_permit']] : $sqlWhere[2];
    }
}
