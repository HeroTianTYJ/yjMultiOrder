$(function () {
  let moduleName = '品牌';

  // 列表
  list(moduleName);

  // 排序
  sort();

  // 添加
  add('添加' + moduleName);

  // 批量添加
  multi('.multi', '批量添加' + moduleName, CONFIG['MULTI']);

  // 修改
  update('修改' + moduleName);

  // 上下架
  $('.list').on('click', 'a.is_view', function () {
    let that = this;
    $.ajax({
      type: 'POST',
      url: CONFIG['IS_VIEW'],
      data: {
        id: $(that).parent().parent().find('input[name=id]').val()
      }
    }).then(function (data) {
      let json = JSON.parse(data);
      showTip(json['content'], json['state']);
      if (json['state'] === 1) {
        if ($(that).attr('attr-is-view') === '0') {
          $(that).parent().html('<span class="green">否</span> | <a href="javascript:" class="is_view" attr-is-view="1">上架</a>');
        } else if ($(that).attr('attr-is-view') === '1') {
          $(that).parent().html('<span class="red">是</span> | <a href="javascript:" class="is_view" attr-is-view="0">下架</a>');
        }
      }
    });
  });

  // 删除
  remove(moduleName);

  // 批量删除
  multiRemove(moduleName);

  // 搜索
  // 关键词
  searchKeyword();
  layui.use(['form'], function () {
    // 品牌分类
    layui.form.on('select(category_id)', function (data) {
      window.location.href = searchUrl('category_id=' + data.value);
    });
    // 微信小程序
    layui.form.on('select(wxxcx_id)', function (data) {
      window.location.href = searchUrl('wxxcx_id=' + data.value);
    });
    // 分销商
    layui.form.on('select(manager_id)', function (data) {
      window.location.href = searchUrl('manager_id=' + data.value);
    });
  });
});

function listItem (item) {
  let control = [];
  control.push('<a href="' + item['url'] + '" target="_blank">访问</a>');
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  if (isPermission('delete')) control.push('<a href="javascript:" class="delete">删除</a>');
  return '<tr class="item' + item['id'] + '"><td' + (isPermission('delete') ? '' : ' class="none"') + '><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td style="color:' + item['color'] + ';">' + item['name'] + '</td><td><img src="' + CONFIG['UPLOAD_DIR'] + item['logo'] + '" alt="' + item['name'] + '"></td><td>' + item['category'] + '</td>' + (isPermission('sort') ? '<td><input type="text" name="sort" value="' + item['sort'] + '" class="text"></td>' : '') + (isPermission('isView') ? '<td>' + (parseInt(item['is_view']) === 0 ? '<span class="green">否</span> | <a href="javascript:" class="is_view" attr-is-view="1">上架</a>' : '<span class="red">是</span> | <a href="javascript:" class="is_view" attr-is-view="0">下架</a>') + '</td>' : '') + '<td>' + item['date'] + '</td><td>' + item['click1'] + '</td><td>' + item['click2'] + '</td>' + (control.length ? '<td>' + control.join('/') + '</td>' : '') + '</tr>';
}
