<?php

namespace yjrj;

use think\facade\Request;
use think\facade\Route;

class Page
{
    public $firstRow;  //起始行数
    private int $totalRows;  //总行数
    private int $pageSize;  //列表每页显示行数
    private array $parameter;  //分页跳转时要带的参数
    private int $nowPage;  //当前页码
    private int $totalPages;  //分页总页面数
    private string $p = 'page';  //分页参数名
    private string $url;  //当前链接URL

    public function __construct($totalRows, $pageSize = 20, $url = '', $parameter = [])
    {
        $this->totalRows = $totalRows;
        $this->pageSize = $pageSize;
        $this->parameter = empty($parameter) ? Request::get() : $parameter;
        $this->nowPage = !Request::get($this->p) ? 1 : intval(Request::get($this->p));
        $this->nowPage = $this->nowPage > 0 ? $this->nowPage : 1;
        $this->firstRow = $this->pageSize * ($this->nowPage - 1);
        $this->parameter[$this->p] = '[PAGE]';
        $parameter = '';
        foreach ($this->parameter as $key => $value) {
            $parameter .= '&' . $key . '=' . $value;
        }
        $this->url = ($url ?: Route::buildUrl('/' . parse_name(Request::controller()) . '/' .
                parse_name(Request::action()))) . '?' . substr($parameter, 1);
        $this->totalPages = ceil($this->totalRows / $this->pageSize);
        if (!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
    }

    private function url($page)
    {
        return str_replace('[PAGE]', $page, $this->url);
    }

    public function show()
    {
        $upRow = $this->nowPage - 1;
        $downRow = $this->nowPage + 1;
        $html = '<form method="get" action="" class="page layui-form"><p>' . $this->nowPage . '/' . $this->totalPages .
            '页（每页' . $this->pageSize . '条/共' . $this->totalRows . '条） | ' . ($this->nowPage > 1 ? '<a href="' .
                $this->url(1) . '">首页</a> |' : '首页 |') . ' ' . ($upRow > 0 ? '<a href="' . $this->url($upRow) .
                '">上一页</a> |' : '上一页 |') . ' ' . ($downRow <= $this->totalPages ? '<a href="' . $this->url($downRow) .
                '">下一页</a> |' : '下一页 |') . ' ' . ($this->nowPage < $this->totalPages ? '<a href="' .
                $this->url($this->totalPages) . '">尾页</a>' : '尾页') .
            ' <select lay-filter="page">';
        for ($i = 1; $i <= $this->totalPages; $i++) {
            $html .= '<option value="' . $this->url($i) . '" ' . ($i == $this->nowPage ? 'selected' : '') . '>第' . $i .
                '页</option>';
        }
        $html .= '</select></p></form>';
        return $html;
    }
}
