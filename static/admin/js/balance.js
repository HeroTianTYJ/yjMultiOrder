$(function () {
  let moduleName = '结算记录';

  // 列表
  list(moduleName);

  // 修改
  update('修改' + moduleName);

  // 删除
  remove(moduleName);

  // 批量删除
  multiRemove(moduleName);

  // 搜索
  layui.use(['form'], function () {
    // 分销商
    layui.form.on('select(manager_id)', function (data) {
      window.location.href = searchUrl('manager_id=' + data.value);
    });
    // 交易类型
    layui.form.on('select(type)', function (data) {
      window.location.href = searchUrl('type=' + data.value);
    });
  });
});

function listItem (item) {
  let control = [];
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  if (isPermission('delete')) control.push('<a href="javascript:" class="delete">删除</a>');
  return '<tr class="item' + item['id'] + '"><td' + (isPermission('delete') ? '' : ' class="none"') + '><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td>' + item['manager'] + '</td><td>' + item['type'] + '</td><td>' + item['price'] + '</td><td>' + item['date'] + '</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}
