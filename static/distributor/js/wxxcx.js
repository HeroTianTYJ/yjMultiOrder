$(function () {
  let moduleName = '微信小程序';

  // 列表
  list(moduleName);

  // 推广
  $('.list').on('click', 'a.share', function () {
    ajaxMessageLayer(CONFIG['SHARE'], '推广', {id: $(this).parent().parent().find('input[name=id]').val()}, function (index) {
      layer.close(index);
    }, function () {
      layui.use(['form'], function () {
        layui.form.render('select');
      });
      iCheck();
    });
  });

  // 搜索
  // 关键词
  searchKeyword();
});

function listItem (item) {
  return '<tr class="item' + item['id'] + '"><td class="none"><div class="check-box"><label><input type="checkbox" name="id" value="' + item['id'] + '"></label></div></td><td>' + item['name'] + '</td><td><a href="javascript:" class="share">推广</a></td></tr>';
}
