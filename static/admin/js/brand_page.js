$(function () {
  let moduleName = '品牌详情页';

  // 列表
  list(moduleName);

  // 修改
  update('修改' + moduleName);
});

function listItem (item) {
  let control = [];
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  return '<tr class="item' + item['id'] + '">' + '<td class="none"><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td>' + item['name'] + '</td><td>' + item['width'] + 'px</td><td>' + item['left_width'] + 'px</td><td>' + item['bg_color'] + '</td><td>' + (parseInt(item['page']) === 0 ? '不分页' : item['page']) + '</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}
