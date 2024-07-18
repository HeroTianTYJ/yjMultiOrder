$(function () {
  let moduleName = '订单';

  // 列表
  list(moduleName);

  // 添加
  add('添加' + moduleName);

  // 分销结算
  $('.tool .balance').on('click', function () {
    ajaxMessageLayer(CONFIG['BALANCE'], '分销结算', {}, function (index) {
      $.ajax({
        type: 'POST',
        url: CONFIG['BALANCE'] + (CONFIG['BALANCE'].indexOf('?') > 0 ? '&' : '?') + 'action=do',
        data: $('form.balance').serialize()
      }).then(function (data) {
        let json = JSON.parse(data);
        showTip(json['content'], json['state']);
        if (json['state'] === 1) {
          layer.close(index);
          setTimeout(function () {
            window.location.reload(true);
          }, 3000);
        }
      });
    });
  });

  // 搜索
  layui.use(['date'], function () {
    // 下单时间
    layui.date.render({
      elem: 'input[name=date1]'
    });
    layui.date.render({
      elem: 'input[name=date2]'
    });
  });
  // 数字调节器
  let $number = $('div.number');
  if ($number.length) {
    $number.number({
      width: 86,
      top: 11,
      min: 0
    });
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
  control.push('<a href="javascript:" class="detail">详情</a>');
  return '<tr class="item' + item['id'] + '"><td class="none"><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td>' + item['order_id'] + '</td><td>' + item['template'] + '</td><td>' + item['product'] + '</td><td>' + item['attr'] + '</td><td>' + item['price'] + '元</td><td>' + item['count'] + '</td><td>' + item['total'] + '元</td><td>' + item['commission'] + '</td><td>' + item['date'] + '</td><td>' + item['payment'] + '</td><td>' + item['order_state'] + '</td><td>' + item['express'] + '</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}
