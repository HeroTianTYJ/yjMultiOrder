$(function () {
  let moduleName = '提现记录';

  // 列表
  list(moduleName);

  // 搜索
  // 关键词
  searchKeyword();
  layui.use(['form'], function () {
    // 提现状态
    layui.form.on('select(state)', function (data) {
      window.location.href = searchUrl('state=' + data.value);
    });
  });
});

function listItem (item) {
  return '<tr class="item' + item['id'] + '"><td>' + item['price'] + '</td><td>' + item['state'] + '</td><td>' + item['date'] + '</td></tr>';
}
