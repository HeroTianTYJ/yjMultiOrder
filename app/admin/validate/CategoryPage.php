<?php

namespace app\admin\validate;

use app\common\validate\Base;

class CategoryPage extends Base
{
    protected $rule = [
        'name' => 'require|max:50',
        'title' => 'max:255',
        'keyword' => 'max:255',
        'description' => 'max:255',
        'width' => 'require|number',
        'left_width' => 'require|number',
        'bg_color' => 'require|max:20',
        'copyright' => 'max:255',
        'code_type' => 'require|between:0,1',
        'code' => 'max:65535',
        'nav' => 'max:65535',
        'icon' => 'max:255',
        'share_title' => 'max:70',
        'share_pic' => 'max:25',
        'share_desc' => 'max:120',
    ];
    protected $message = [
        'name' => '页面名称不得为空或大于50位！',
        'title' => '网页标题不得大于255位！',
        'keyword' => '网页关键词不得大于255位！',
        'description' => '网页描述不得大于255位！',
        'width' => '网页宽度必须是数字！',
        'left_width' => '左边栏宽度必须是数字！',
        'bg_color' => '网页背景不得为空或大于20位！',
        'copyright' => '底部版权不得大于255位！',
        'code_type' => '第三方代码类型不合法！',
        'code' => '第三方代码不得大于65535位！',
        'nav' => '导航链接不得大于65535位！',
        'icon' => '导航图标不得大于255位！',
        'share_title' => '分享标题不得大于70位！',
        'share_pic' => '分享预览图不得大于25位！',
        'share_desc' => '分享摘要不得大于120位！',
    ];
}
