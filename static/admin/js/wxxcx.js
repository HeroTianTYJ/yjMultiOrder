$(function () {
  let moduleName = '微信小程序';
  let $list = $('.list');

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

  // 打包
  $list.on('click', 'a.zip', function () {
    let that = this;
    let id = $(that).parent().parent().find('input[name=id]').val();
    $.ajax({
      type: 'POST',
      url: CONFIG['ZIP'],
      data: {
        id: id
      }
    }).then(function (data) {
      let json = JSON.parse(data);
      showTip(json['content'], json['state']);
      if (json['state'] === 1) {
        $(that).parent().html('已打包 | ' + zipLink(id).join('/'));
      }
    });
  });

  // 删除压缩包
  $list.on('click', 'a.delete_zip', function () {
    let that = this;
    $.ajax({
      type: 'POST',
      url: CONFIG['DELETE_ZIP'],
      data: {
        id: $(that).parent().parent().find('input[name=id]').val()
      }
    }).then(function (data) {
      let json = JSON.parse(data);
      showTip(json['content'], json['state']);
      if (json['state'] === 1) {
        $(that).parent().html('未打包' + (isPermission('zip') ? ' | <a href="javascript:" class="zip">打包</a>' : ''));
      }
    });
  });

  // 搜索
  // 关键词
  searchKeyword();
  layui.use(['form'], function () {
    // 页面类型
    layui.form.on('select(type)', function (data) {
      window.location.href = searchUrl('type=' + data.value);
    });
    // 列表页
    layui.form.on('select(lists_id)', function (data) {
      window.location.href = searchUrl('lists_id=' + data.value);
    });
    // 商品页
    layui.form.on('select(item_id)', function (data) {
      window.location.href = searchUrl('item_id=' + data.value);
    });
  });
});

function listItem (item) {
  let control = [];
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  if (isPermission('delete')) control.push('<a href="javascript:" class="delete">删除</a>');
  return '<tr class="item' + item['id'] + '"><td' + (isPermission('delete') ? '' : ' class="none"') + '><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td>' + item['name'] + '</td><td>' + item['type'] + '</td><td>' + item['page'] + '</td>' + (zipLink(item['id']).length ? '<td>' + (item['zip'] ? '已打包 | ' + zipLink(item['id']).join('/') : '未打包' + (isPermission('zip') ? ' | <a href="javascript:" class="zip">打包</a>' : '')) + '</td>' : '') + '<td>' + item['date'] + '</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}

function zipLink (id) {
  let link = [];
  if (isPermission('download')) link.push('<a href="' + CONFIG['DOWNLOAD'] + '?id=' + id + '">下载</a>');
  if (isPermission('zip')) link.push('<a href="javascript:" class="zip">重新打包</a>');
  if (isPermission('deleteZip')) link.push('<a href="javascript:" class="delete_zip">删除压缩包</a>');
  return link;
}
