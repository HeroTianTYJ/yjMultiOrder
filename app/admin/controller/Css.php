<?php

namespace app\admin\controller;

use app\admin\library\Html;
use app\admin\model;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\facade\View;

class Css extends Base
{
    private array $type = ['公共', '前台', '主后台', '分销商后台'];
    private array $file = [
        'yj.admin.ui' => [
            'basic' => '基本样式',
            'gallery' => '图片库',
            'single_page' => '后台单页公共样式',
            'tip' => '信息提示页'
        ],
        'index' => [
            'basic' => '基本样式',
            'brand' => '品牌详情页',
            'category' => '品牌分类页',
            'index' => '列表页',
            'item' => '商品详情页',
            'order' => '订单查询',
            'pay' => '订单支付',
            'template1' => '手机版下单页1、2',
            'template2' => '手机版下单页3',
            'template3' => '手机版下单页4',
            'template4' => '电脑版下单页'
        ],
        'admin' => [
            'bill' => '账单管理',
            'bill_statistic' => '账单统计',
            'brand' => '品牌管理',
            'captcha' => '验证码',
            'category' => '品牌分类',
            'css' => 'CSS管理',
            'database' => '数据表状态',
            'database_backup' => '数据库备份',
            'index' => '后台首页',
            'install' => '系统安装',
            'item' => '商品页',
            'login' => '登录',
            'manager' => '管理员/分销商',
            'message' => '留言管理',
            'message_board' => '留言板',
            'order' => '订单管理',
            'order_output' => '订单导出设置',
            'order_recycle' => '订单回收站',
            'order_statistic' => '订单统计',
            'order_ui' => '订单界面设置',
            'permit_group' => '权限组',
            'picture' => '图片管理',
            'product' => '商品管理',
            'profile' => '个人中心',
            'reset' => '重置密码',
            'smtp' => 'SMTP服务器',
            'system' => '系统设置',
            'template_style' => '模板样式',
            'validate_file' => '生成验证文件',
            'visit_wxxcx' => '访问统计（微信小程序）',
            'withdraw' => '提现管理',
            'wxxcx' => '微信小程序'
        ],
        'distributor' => [
            'index' => '后台首页',
            'login' => '登录页',
            'order' => '订单',
            'order_statistic' => '订单统计',
            'profile' => '个人中心',
            'register' => '注册',
            'visit_wxxcx' => '访问统计（微信小程序）',
            'wxxcx' => '微信小程序'
        ]
    ];

    public function index()
    {
        $cssAll = (new model\Css())->all();
        if (Request::isAjax()) {
            foreach ($cssAll as $key => $value) {
                $cssAll[$key] = $this->listItem($value);
            }
            return $cssAll->items() ? json_encode($cssAll->items()) : '';
        }
        View::assign(['Total' => $cssAll->total()]);
        Html::typeSelect($this->type, Request::get('type', -1));
        return $this->view();
    }

    public function add()
    {
        $Css = new model\Css();
        foreach ($this->file['yj.admin.ui'] as $key => $value) {
            $Css->add(0, $key, $value);
        }
        foreach ($this->file['index'] as $key => $value) {
            $Css->add(1, $key, $value);
        }
        foreach ($this->file['admin'] as $key => $value) {
            $Css->add(2, $key, $value);
        }
        foreach ($this->file['distributor'] as $key => $value) {
            $Css->add(3, $key, $value);
        }
        return $this->succeed(Route::buildUrl('/' . parse_name(Request::controller()) . '/index'), 'CSS文件添加成功！');
    }

    public function update()
    {
        if (Request::isAjax() && Request::post('id')) {
            $Css = new model\Css();
            $cssOne = $Css->one();
            if (!$cssOne) {
                return showTip('不存在此CSS文件！', 0);
            }
            $filepath = ROOT_DIR . '/static/';
            if ($cssOne['type'] == 0) {
                $filepath .= 'yj.admin.ui';
            } elseif ($cssOne['type'] == 1) {
                $filepath .= 'index';
            } elseif ($cssOne['type'] == 2) {
                $filepath .= 'admin';
            } elseif ($cssOne['type'] == 3) {
                $filepath .= 'distributor';
            }
            $filepath .= '/css/' . $cssOne['filename'];
            if (Request::get('action') == 'do') {
                if (Config::get('app.demo')) {
                    return showTip('演示站，CSS文件无法修改！', 0);
                }
                file_put_contents($filepath, Request::post('code', '', 'htmlspecialchars_decode'));
                return showTip([
                    'msg' => 'CSS文件修改成功！',
                    'data' => $this->listItem($Css->one())
                ]);
            }
            $cssOne['type'] = $this->type[$cssOne['type']];
            $cssOne['code'] = htmlspecialchars(file_get_contents($filepath));
            View::assign(['One' => $cssOne]);
            return $this->view();
        } else {
            return showTip('非法操作！', 0);
        }
    }

    public function output()
    {
        return '<meta charset="utf-8"><pre>文件夹内所有的：
' . $this->getStr() . '
文件夹内未添加的：
' . $this->getStr(1) . '
已添加但文件夹内不存在的：
' . $this->getStr(2) . '</pre>';
    }

    private function txt($module, $type = 0)
    {
        if ($type != 2) {
            $arrStr = "'" . $module . "' => [";
            foreach (scandir(ROOT_DIR . '/static/' . $module . '/css') as $value) {
                if (!in_array($value, ['.', '..'])) {
                    $name = substr($value, 0, -4);
                    if ($type == 0) {
                        $arrStr .= "
                '" . $name . "' => '',";
                    } elseif ($type == 1) {
                        if (!isset($this->file[$module][$name])) {
                            $arrStr .= "
                '" . $name . "' => '',";
                        }
                    }
                }
            }
            $arrStr = ($type == 0 ? substr($arrStr, 0, -1) : $arrStr) . '
        ],';
        } else {
            $arrStr = '';
            foreach ($this->file as $key => $value) {
                $arrStr .= "
        '" . $key . "' => [";
                foreach ($value as $k => $v) {
                    if (!is_file(ROOT_DIR . '/static/' . $key . '/css/' . $k . '.css')) {
                        $arrStr .= "
                '" . $k . "' => '" . $v . "',";
                    }
                }
                $arrStr .= '
        ],';
            }
        }
        return $arrStr;
    }

    private function getStr($type = 0)
    {
        $arrStr = '    private array $file = [
        ';
        if ($type != 2) {
            $arrStr .= $this->txt('yj.admin.ui', $type) . '
        ';
            $arrStr .= $this->txt('index', $type) . '
        ';
            $arrStr .= $this->txt('admin', $type) . '
        ';
            $arrStr .= substr($this->txt('distributor', $type), 0, -1);
        } else {
            $arrStr .= substr($this->txt('', $type), 4, -1);
        }
        $arrStr .= '
    ];';
        return $arrStr;
    }

    private function listItem($item)
    {
        $item['filename'] = keyword($item['filename']);
        $item['type'] = $this->type[$item['type']];
        $item['description'] = keyword($item['description']);
        return $item;
    }
}
