<?php

namespace app\admin\model;

use app\admin\validate\Captcha as validate;
use think\facade\Request;
use think\Model;

class Captcha extends Model
{
    //验证
    public function form()
    {
        $data = [
            'name' => str_replace('\'', '\\\'', Request::post('name')),
            'type' => Request::post('type'),
            'length' => Request::post('type') != 2 ? Request::post('length') : 0,
            'fontSize' => Request::post('fontSize'),
            'imageW' => Request::post('imageW') ? Request::post('imageW') : 0,
            'imageH' => Request::post('imageH') ? Request::post('imageH') : 0,
            'bgR' => Request::post('bgR'),
            'bgG' => Request::post('bgG'),
            'bgB' => Request::post('bgB'),
            'useImgBg' => Request::post('useImgBg'),
            'useCurve' => Request::post('useCurve'),
            'useNoise' => Request::post('useNoise')
        ];
        $validate = new validate();
        if ($validate->check($data)) {
            return $data;
        } else {
            return implode($validate->getError());
        }
    }
}
