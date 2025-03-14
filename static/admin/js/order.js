$(function () {
  let moduleName = '订单';
  let $tool = $('.tool');

  // 列表
  list(moduleName);

  // 添加
  add('添加' + moduleName);

  // 修改
  update('修改' + moduleName);

  // 删除
  remove(moduleName, true);

  // 批量删除
  multiRemove(moduleName, true);

  // 修改订单状态
  multi('.state', '修改订单状态', CONFIG['STATE']);

  // 修改快递单号
  multi('.express', '修改快递单号', CONFIG['EXPRESS']);

  // 导出当前订单
  $tool.find('.output1').on('click', function () {
    download(CONFIG['OUTPUT'] + '?' + window.location.toString().split('?')[1], {type: 0, siwu: $tool.find('input[name=siwu]:checked').val()});
  });

  // 导出选定订单
  $tool.find('.output2').on('click', function () {
    download(CONFIG['OUTPUT'], {type: 1, ids: $tool.find('input[name=ids]').val(), siwu: $tool.find('input[name=siwu]:checked').val()});
  });

  // 搜索
  layui.use(['form', 'date'], function () {
    // 下单时间
    layui.date.render({
      elem: 'input[name=date1]'
    });
    layui.date.render({
      elem: 'input[name=date2]'
    });
    // 支付时间
    layui.date.render({
      elem: 'input[name=pay_date1]'
    });
    layui.date.render({
      elem: 'input[name=pay_date2]'
    });
    // 支付方式
    layui.form.on('select(payment_id)', function (data) {
      payment(data.value);
    });
  });
  // 数字调节器
  $('div.number').number({
    width: 86,
    top: 11,
    min: 0
  });
  // 支付方式
  let $alipayScene = $tool.find('.alipay_scene');
  let $wechatPayScene = $tool.find('.wechat_pay_scene');
  payment($tool.find('select[name=payment_id] option:selected').val());
  function payment (val) {
    switch (val) {
      case '2':
        $alipayScene.show();
        $wechatPayScene.hide();
        break;
      case '3':
        $alipayScene.hide();
        $wechatPayScene.show();
        break;
      default:
        $alipayScene.hide();
        $wechatPayScene.hide();
    }
  }

  // 详情
  $('.list').on('click', 'a.detail', function () {
    ajaxMessageLayer(CONFIG['DETAIL'], '订单详情', {id: $(this).parent().parent().find('input[name=id]').val()}, function (index) {
      layer.close(index);
    });
  });
});

function listItem (item) {
  let control = [];
  if (isPermission('detail')) control.push('<a href="javascript:" class="detail">详情</a>');
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  if (isPermission('delete')) control.push('<a href="javascript:" class="delete">删除</a>');
  return '<tr class="item' + item['id'] + '"><td' + (isPermission('delete') ? '' : ' class="none"') + '><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td>' + item['html'] + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}
