<?php

namespace app\index\controller;

use think\facade\Request;

class Qrcode extends Base
{
    public function index()
    {
        return '<p style="text-align:center;"><img src="' . Request::post('qrcode') .
            '" alt="二维码" style="width:90%;object-fit:contain;"></p>';
    }
}
