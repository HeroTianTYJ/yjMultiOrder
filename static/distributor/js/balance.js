$(function () {
  let moduleName = '结算记录';

  // 列表
  list(moduleName);

  // 添加
  add('申请提现');

  // 搜索
  // 关键词
  searchKeyword();
  layui.use(['form'], function () {
    // 交易类型
    layui.form.on('select(type)', function (data) {
      window.location.href = searchUrl('type=' + data.value);
    });
  });
});

function listItem (item) {
  return '<tr class="item' + item['id'] + '"><td>' + item['type'] + '</td><td>' + item['price'] + '</td><td>' + item['date'] + '</td></tr>';
}
