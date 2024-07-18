$(function () {
  let moduleName = '账单统计';

  // 列表
  list(moduleName);

  // 导出统计结果
  $('.tool .output').on('click', function () {
    download(CONFIG['OUTPUT'], {time: $(this).attr('time')});
  });

  // 搜索
  layui.use(['form', 'date'], function () {
    // 时间
    layui.date.render({
      elem: 'input[name=date1]'
    });
    layui.date.render({
      elem: 'input[name=date2]'
    });
  });
  // 数字调节器
  $('div.number').number({
    width: 86,
    top: 11,
    min: 0
  });
});

function listItem (item) {
  if (CONFIG['TYPE'] === 'index') {
    return '<tr class="item' + item['id'] + '"><td>' + item['time'] + '</td><td>' + item['data']['all_in_count'] + '</td><td>' + item['data']['all_in_price'] + '元</td><td>' + item['data']['all_out_count'] + '</td><td>' + item['data']['all_out_price'] + '元</td><td>' + item['data']['all_add_count'] + '</td><td>' + item['data']['all_add_price'] + '元</td></tr>';
  } else {
    return '<tr class="item' + item['id'] + (item['time'] === '合计' ? ' footer' : '') + '"><td>' + item['time'] + '</td><td>' + item['all_in_count'] + '</td><td>' + item['all_in_price'] + '元</td><td>' + item['all_out_count'] + '</td><td>' + item['all_out_price'] + '元</td><td>' + item['all_add_count'] + '</td><td>' + item['all_add_price'] + '元</td></tr>';
  }
}
