<?php

namespace app\index\controller;

use think\facade\Request;

class Error extends Base
{
    public function index()
    {
        return (new Index())->index(Request::controller());
    }
}
