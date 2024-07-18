<?php

namespace app\admin\controller;

use app\admin\library\Html;
use think\facade\Config;
use think\facade\Request;

class OrderOutput extends Base
{
    public function index()
    {
        if (Request::isAjax()) {
            if (Config::get('app.demo')) {
                return showTip('演示站，订单导出设置无法修改！', 0);
            }
            $output = '<?php

return [';
            foreach (Request::post('sort') as $value) {
                $output .= '
    ' . $value . ' => ' . (Request::post('selected.' . $value, 0) == 0 ? 0 : 1) . ',';
            }
            $output = substr($output, 0, -1) . '
];
';
            return file_put_contents(ROOT_DIR . '/app/admin/config/order_output.php', $output) ?
                showTip('订单导出设置修改成功！') : showTip('订单导出设置修改失败！', 0);
        }
        Html::orderOutputSort((new Order())->output);
        return $this->view();
    }

    public function restore()
    {
        return file_put_contents(ROOT_DIR . '/app/admin/config/order_output.php', '<?php

return [];
') ? showTip('订单导出设置重置成功！') : showTip('订单导出设置重置失败！', 0);
    }
}
