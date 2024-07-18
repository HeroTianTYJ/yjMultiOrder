<?php

namespace app\index\controller;

use app\index\model;
use think\captcha\facade\Captcha;
use think\facade\Config;
use think\facade\Request;
use yjrj\QQWry;

class Common extends Base
{
    //访问统计
    public function visit()
    {
        $Visit = new model\Visit();
        if ($Visit->yesterday()) {
            $output = '"IP","推广分销商","访问页面","当日次数","第一次","最后一次",';
            $visitAll = $Visit->all();
            if ($visitAll) {
                $Manager = new model\Manager();
                foreach ($visitAll as $value) {
                    if ($value['manager_id']) {
                        $managerOne = $Manager->one($value['manager_id']);
                        $managerName = $managerOne ? $managerOne['name'] : '此分销商已被删除';
                    } else {
                        $managerName = '';
                    }
                    $output .= "\r\n" . '"' . $value['ip'] . ' -- ' . QQWry::getAddress($value['ip']) . '","' .
                        $managerName . '","' . $value['url'] . '","' . $value['count'] . '","' .
                        dateFormat($value['date1']) . '","' . dateFormat($value['date2']) . '",';
                }
            }
            $output = mb_convert_encoding($output, 'GBK', 'UTF-8');
            $file = Config::get('dir.output') . 'visit_' . date('YmdHis') . '.csv';
            if (file_put_contents(ROOT_DIR . '/' . $file, $output)) {
                $Visit->truncate();
            }
        }
        $visitAll = $Visit->one();
        $visitAll ? $Visit->modify($visitAll['id']) : $Visit->add();
    }

    //验证码
    public function captcha()
    {
        if (Request::get('id')) {
            $captcha = Config::get('captcha');
            return isset($captcha[Request::get('id')]) ? Captcha::create(Request::get('id')) : '';
        }
        return '';
    }
    public function captcha2()
    {
        return Captcha::create();
    }

    //地区联动
    public function district()
    {
        return json_encode((new model\District())->all());
    }
}
