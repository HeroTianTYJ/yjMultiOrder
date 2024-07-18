$(function () {
  let moduleName = '账单';
  let $tool = $('.tool');

  // 列表
  list(moduleName);

  // 添加
  add('添加' + moduleName);

  // 修改
  update('修改' + moduleName);

  // 删除
  remove(moduleName);

  // 批量删除
  multiRemove(moduleName);

  // 导出当前账单
  $tool.find('.output1').on('click', function () {
    download(CONFIG['OUTPUT'] + '?' + window.location.toString().split('?')[1], {type: 0});
  });

  // 导出选定账单
  $tool.find('.output2').on('click', function () {
    download(CONFIG['OUTPUT'], {type: 1, ids: $tool.find('input[name=ids]').val()});
  });

  // 更新收支余
  $tool.find('.update2').on('click', function () {
    $.ajax({
      type: 'POST',
      url: CONFIG['UPDATE2'],
      data: {
        rows: $tool.find('input[name=rows]').val()
      }
    }).then(function (data) {
      let json = JSON.parse(data);
      showTip(json['content'], json['state']);
      if (json['state'] === 1) {
        setTimeout(function () {
          window.location.reload(true);
        }, 3000);
      }
    });
  });

  // 搜索
  layui.use(['date'], function () {
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

  // 详情
  $('.list').on('click', 'a.detail', function () {
    ajaxMessageLayer(CONFIG['DETAIL'], '账单详情', {id: $(this).parent().parent().find('input[name=id]').val()}, function (index) {
      layer.close(index);
    });
  });
});

function listItem (item) {
  let control = [];
  if (isPermission('detail')) control.push('<a href="javascript:" class="detail">详情</a>');
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  if (isPermission('delete')) control.push('<a href="javascript:" class="delete">删除</a>');
  return '<tr class="item' + item['id'] + '"><td' + (isPermission('delete') ? '' : ' class="none"') + '><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td>' + item['name'] + '</td><td>' + item['manager'] + '</td><td>' + item['bill_sort'] + '</td><td>' + (item['in'] ? item['in_price'] + '元×' + item['in_count'] : '') + '</td><td>' + (item['in_out'] ? item['in_price_out'] + '元×' + item['in_count_out'] : '') + '</td><td>' + (item['out'] ? item['out_price'] + '元×' + item['out_count'] : '') + '</td><td>' + (item['out_in'] ? item['out_price_in'] + '元×' + item['out_count_in'] : '') + '</td><td>' + item['add'] + '</td><td>' + item['all_in'] + '元</td><td>' + item['all_out'] + '元</td><td>' + item['all_add'] + '元</td><td>' + item['date'] + '</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}
