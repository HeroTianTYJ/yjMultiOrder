$(function () {
  let moduleName = '留言';

  // 列表
  list(moduleName);

  // 修改
  update('修改' + moduleName);

  // 删除
  remove(moduleName);

  // 批量删除
  multiRemove(moduleName);

  // 精选/取消精选
  $('.list').on('click', 'a.is_view', function () {
    let that = this;
    $.ajax({
      type: 'POST',
      url: CONFIG['IS_VIEW'],
      data: {
        id: $(that).parent().parent().parent().find('input[name=id]').val()
      }
    }).then(function (data) {
      let json = JSON.parse(data);
      showTip(json['content'], json['state']);
      if (json['state'] === 1) {
        if ($(that).attr('attr-is-view') === '0') {
          $(that).parent().html('<i class="green">否</i> | <a href="javascript:" class="is_view" attr-is-view="1">精选</a>');
        } else if ($(that).attr('attr-is-view') === '1') {
          $(that).parent().html('<i class="red">是</i> | <a href="javascript:" class="is_view" attr-is-view="0">取消精选</a>');
        }
      }
    });
  });

  // 搜索
  // 关键词
  searchKeyword();
  layui.use(['form'], function () {
    // 留言板
    layui.form.on('select(message_board_id)', function (data) {
      window.location.href = searchUrl('message_board_id=' + data.value);
    });
    // 精选状态
    layui.form.on('select(is_view)', function (data) {
      window.location.href = searchUrl('is_view=' + data.value);
    });
  });
});

function listItem (item) {
  let control = [];
  if (isPermission('update')) control.push('<a href="javascript:" class="update">修改</a>');
  if (isPermission('delete')) control.push('<a href="javascript:" class="delete">删除</a>');
  return '<li class="item' + item['id'] + '"><div class="item">' + (isPermission('delete') ? '<div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div>' : '') + ' <span class="name">姓名：</span>' + item['name'] + ' <span>联系电话：</span>' + item['tel'] + ' <span>电子邮箱：</span>' + item['email'] + '　' + control.join('/') + '</div><div class="item"><span>留言IP：</span>' + item['ip'] + ' <span>所属留言板：</span>' + item['message_board'] + ' <span>留言时间：</span>' + item['date'] + ' ' + (isPermission('isView') ? '<span>是否精选：</span>' + (parseInt(item['is_view']) === 0 ? '<i><i class="green">否</i> | <a href="javascript:" class="is_view" attr-is-view="1">精选</a></i>' : '<i><i class="red">是</i> | <a href="javascript:" class="is_view" attr-is-view="0">取消精选</a></i>') : '') + '</div><div class="item"><span>留言内容：</span>' + item['content'] + '</div><div class="item"><span>回复内容：</span>' + item['reply'] + '</div></li>';
}
