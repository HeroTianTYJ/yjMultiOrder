$(function () {
  let moduleName = '列表页';

  // 列表
  list(moduleName);

  // 搜索
  // 关键词
  searchKeyword();
  layui.use(['form'], function () {
    // 微信小程序
    layui.form.on('select(wxxcx_id)', function (data) {
      window.location.href = searchUrl('wxxcx_id=' + data.value);
    });
  });
});

function listItem (item) {
  return '<tr class="item' + item['id'] + '"><td>' + item['name'] + '</td><td>' + item['click1'] + '</td><td>' + item['click2'] + '</td><td><a href="' + item['url'] + '" target="_blank">' + item['url'] + '</a></td></tr>';
}
