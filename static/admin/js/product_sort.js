$(function () {
  let moduleName = '商品分类';

  // 列表
  list(moduleName);

  // 排序
  sort();

  // 添加
  add('添加' + moduleName);

  // 修改
  update('修改' + moduleName);

  // 删除
  remove(moduleName);

  // 批量删除
  multiRemove(moduleName);

  // 搜索
  // 关键词
  searchKeyword();
});

function listItem (item) {
  let control = [];
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  if (isPermission('delete')) control.push('<a href="javascript:" class="delete">删除</a>');
  return '<tr class="item' + item['id'] + '"><td' + (isPermission('delete') ? '' : ' class="none"') + '><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td style="color:' + item['color'] + ';">' + item['name'] + '</td>' + (isPermission('sort') ? '<td><input type="text" name="sort" value="' + item['sort'] + '" class="text"></td>' : '') + '<td>' + item['count'] + '</td><td>' + item['date'] + '</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}
