<?php

namespace app\admin\validate;

use app\common\validate\Base;

class Lists extends Base
{
    protected $rule = [
        'name' => 'require|max:30',
        'module' => 'max:20|regex:/^[a-zA-Z][a-zA-Z0-9]*$/',
        'title' => 'max:255',
        'keyword' => 'max:255',
        'description' => 'max:255',
        'width' => 'require|number',
        'bg_color' => 'require|max:20',
        'copyright' => 'max:255',
        'code' => 'max:65535',
        'page' => 'number',
        'share_title' => 'max:50',
        'share_desc' => 'max:100',
    ];
    protected $message = [
        'name' => '页面名称不得为空或大于30位！',
        'module' => '页面地址不得大于20位，且必须是字母、数字，且第一位必须是字母！',
        'title' => '网页标题不得大于255位！',
        'keyword' => '网页关键词不得大于255位！',
        'description' => '网页描述不得大于255位！',
        'width' => '网页宽度必须是数字！',
        'bg_color' => '网页背景不得为空或大于20位！',
        'copyright' => '底部版权不得大于255位！',
        'code' => '第三方代码不得大于65535位！',
        'page' => '每页条数必须是数字！',
        'share_title' => '分享标题不得大于50位！',
        'share_desc' => '分享摘要不得大于100位！',
    ];
}
