$(function () {
  let moduleName = '验证码';

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
  control.push('<a href="' + CONFIG['WEB_URL'] + 'common/captcha.html?id=' + item['id'] + '" target="_blank">预览</a>');
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  if (isPermission('delete')) control.push('<a href="javascript:" class="delete">删除</a>');
  return '<tr class="item' + item['id'] + '"><td' + (isPermission('delete') ? '' : ' class="none"') + '><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td>' + item['name'] + '</td><td>' + item['type'] + '</td><td>' + item['length'] + '</td><td>' + item['fontSize'] + 'px</td><td>' + item['imageW'] + 'px</td><td>' + item['imageH'] + 'px</td><td><span style="background:rgb(' + item['bg'][0] + ',' + item['bg'][1] + ',' + item['bg'][2] + ');"></span></td><td>' + item['useImgBg'] + '</td><td>' + item['useCurve'] + '</td><td>' + item['useNoise'] + '</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}
