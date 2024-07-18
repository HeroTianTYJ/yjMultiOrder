<?php

namespace app\admin\controller;

use app\admin\library\Html;
use think\facade\Config;
use think\facade\Request;

class OrderUi extends Base
{
    public function index()
    {
        if (Request::isAjax()) {
            if (Config::get('app.demo')) {
                return showTip('演示站，无法修改订单界面设置！', 0);
            }
            $output = "<?php

return [
    'reminder' => " . (Request::post('reminder') == 0 ? 0 : 1) . ",
    'search' => " . (Request::post('search') == 0 ? 0 : 1) . ",
    'search_sort' => [";
            foreach (Request::post('search_sort') as $value) {
                $output .= '
        ' . $value . ' => ' . (Request::post('search_selected.' . $value, 0) == 0 ? 0 : 1) . ',';
            }
            $output = substr($output, 0, -1) . "
    ],
    'list' => [";
            foreach (Request::post('list_sort') as $value) {
                if (!is_numeric(Request::post('list_width.' . $value))) {
                    return showTip('表格宽度必须是数字！', 0);
                }
                $output .= '
        ' . $value . ' => [' . Request::post('list_width.' . $value) . ', ' .
                    (Request::post('list_selected.' . $value, 0) == 0 ? 0 : 1) . '],';
            }
            $output = substr($output, 0, -1) . "
    ],
    'detail_sort' => [";
            foreach (Request::post('detail_sort') as $value) {
                $output .= '
        ' . $value . ' => ' . (Request::post('detail_selected.' . $value, 0) == 0 ? 0 : 1) . ',';
            }
            $output = substr($output, 0, -1) . '
    ]
];
';
            return file_put_contents(ROOT_DIR . '/app/admin/config/order_ui.php', $output) ?
                showTip('订单界面设置修改成功！') : showTip('订单界面设置修改失败！', 0);
        }
        $Order = new Order();
        Html::orderSearchSort($Order->search);
        Html::orderList($Order->list);
        Html::orderDetailSort($Order->detail);
        return $this->view();
    }

    public function restore()
    {
        return file_put_contents(ROOT_DIR . '/app/admin/config/order_ui.php', '<?php

return [];
') ? showTip('订单界面设置重置成功！') : showTip('订单界面设置重置失败！', 0);
    }
}
